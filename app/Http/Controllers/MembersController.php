<?php

namespace App\Http\Controllers;

use App\Models\Members;
use App\Models\MembersFriends;
use App\Models\MembersInformation;
use Exception;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    //атворизовать пользователя
    public function auth($member)
    {
        if (!$member['auth_hash']) {
            $hash = $this->makeAuthHash($member);
        } else {
            $hash = $member['auth_hash'];
        }

        setcookie('auth_hash', $hash, (time() + (60 * 60 * 24 * 31 * 3)), '/');


        $member->update(['auth_hash' => $hash]);
        $member->save();
    }

    //Метод для страницы с не разглошением информации
    public function memberNonDisclosure(Request $request)
    {
        $member = $this->getAuthorizedMember();
        if ($member) {
            $memberInformation = MembersInformation::where('member_id', $member['id'])->where('field_name', 'non_disclosure')->first();
            if (empty($memberInformation)) {
                $memberInformation = new MembersInformation([
                    'member_id' => $member['id'],
                    'field_name' => 'non_disclosure',
                    'value' => 1,
                    'public' => 0
                ]);
            } else {
                $memberInformation->update(['value' => 1, 'public' => 0]);
            }
            $memberInformation->save();
            return redirect('/');
        }
        return redirect('/auth');
    }

    public function checkAdmin()
    {
        $member = $this->getAuthorizedMember();

        if ($member && ($member['role'] == 'admin' || $member['role'] == 'root')) {
            return response()->json(['status' => 'ok']);
        }
        return response()->json(['status' => 'error']);
    }

    public function memberLogOut()
    {
        $member = $this->getAuthorizedMember();

        if ($member) {
            $member->update(['auth_hash' => '']);
            $member->save();

            setcookie('auth_hash', false, (time() + (60 * 60 * 24 * 31 * 10)), '/');
            return response()->json(['status' => 'ok']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }

    //Метод для страницы, личного кабинета другого пользователя
    public function otherMemberProfile($member_dev){
        $member = Members::where('dev_key', $member_dev)->first();
        return view('profile/profile/member_profile', ['data' => $member]);
    }

    //Метод для страницы, личного кабинета
    public function memberProfile()
    {
        $member = $this->getAuthorizedMember();
        $member_information = $this->getAuthorizedMemberInformation($member['id']);
        $error_messages = [];

        $value_names = ['name', 'max_start_sum', 'whos_i'];
        $memberInformation = MembersInformation::where('member_id', $member['id'])->whereIn('field_name', $value_names)->count();
        if ($memberInformation < 3)
            $error_messages = ['Для продолжения, укажите свое имя и информацию о себе'];

        return view('profile/profile/profile', ['data' => $member_information, 'error_messages' => $error_messages]);
    }

    public function memberProfileSend(Request $request)
    {
        $values = [
            'name' => $request->input('name'),
            'l_name' => $request->input('lname'),
            'whos_i' => $request->input('whosi'),
            'public_whos_i' => $request->has('public_whosi'),
            'max_start_sum' => $request->input('maxStartSum'),
            'public_max_start_sum' => $request->has('public_max_start_sum')
        ];
        $member = $this->getAuthorizedMember();
        $error_messages = [];

        if (!$values['name'] || !$values['whos_i'] || !$values['max_start_sum'])
            $error_messages[] = 'Пожалуйста, укажите все важные поля!';
        else {
            $values_names = array_keys($values);
            foreach ($values_names as $value_name) {
                $public = 0;
                if (strpos($value_name, 'public_') !== false) {
                    if ($values[$value_name])
                        $public = 1;

                    $value_name_2 = str_replace('public_', '', $value_name);
                    $value_name = $value_name_2;

                }

                if ($values[$value_name]) {
                    if ($value_name == 'name' || $value_name == 'l_name')
                        $public = 1;

                    $member_information = MembersInformation::where('member_id', $member['id'])->where('field_name', $value_name)->first();

                    if (empty($member_information)) {
                        $member_information = new MembersInformation([
                            'member_id' => $member['id'],
                            'field_name' => $value_name,
                            'value' => $values[$value_name],
                            'public' => $public
                        ]);
                    } else {
                        $member_information->update(['value' => $values[$value_name], 'public' => $public]);
                    }

                    $member_information->save();
                }
            }
        }

        return view('profile/profile/profile', ['error_messages' => $error_messages, 'data' => $values]);
    }

    //Получить массив id друзей member-а
    public function getMemberFriendsArray($member_id)
    {
        $member_friends_array = [];
        foreach (MembersFriends::where('first_member_id', $member_id)->orWhere('second_member_id', $member_id)->get() as $mf) {
            if ($mf['first_member_id'] != $member_id) {
                $member_friends_array[] = $mf['first_member_id'];
            } else {
                $member_friends_array[] = $mf['second_member_id'];
            }
        }

        return $member_friends_array;
    }

    //Удаление/добавление пользователя в друзья
    public function friendsAddDelete(Request $request){
        $friend_id = $request->input('friend_id');
        $type = $request->input('type');
        $member = $this->getAuthorizedMember();

        $member_friend = MembersFriends::where('first_member_id', $friend_id)->where('second_member_id', $member['id'])
            ->orWhere('first_member_id', $member['id'])->where('second_member_id', $friend_id)->first();
        if($type == 'delete_friend'){
            if(!empty($member_friend))
                $member_friend->delete();
            else
                return response()->json(['status' => 'error', 'data' => '', 'error_code' => 0, 'message' => 'Пользователь не был найден в друзьях, попробуйте перезапустить страницу...']);
        }elseif($type == 'add_friend'){
            if(empty($member_friend)){
                $member_friend = new MembersFriends([
                    'first_member_id' => $member['id'],
                    'second_member_id' => $friend_id
                ]);
                $member_friend->save();
            }else{
                return response()->json(['status' => 'error', 'data' => '', 'error_code' => 1, 'message' => 'Пользователь уже есть в друзьях, попробуйте перезапустить страницу...']);
            }
        }

        return response()->json(['status'=>'ok', 'data' => [], 'error_code' => -1, 'message' => '']);
    }

    //Метод для друзей пользователя
    public function getMembersFriends(Request $request)
    {
        $member = $this->getAuthorizedMember();

        //Опредиление типа
        $type = 'all_members';
        if ($request->has('type'))
            $type = $request->input('type');

        //Получение айдишников всех друзей
        $friends_ids = $this->getMemberFriendsArray($member['id']);

        //Получение всех пользователей
        $friends = Members::where('id', '!=', $member['id'])->paginate(15);

        foreach ($friends as $friend) {
            $friend['is_friend'] = false;
            if (in_array($friend['id'], $friends_ids))
                $friend['is_friend'] = true;

            $values = ['name', 'l_name', 'whos_i', 'profile_img'];
            foreach($values as $val){
                $friend[$val] = MembersInformation::where('public', 1)->where('member_id', $friend['id'])->where('field_name', $val)->pluck('value');
                if($val == 'name' && empty($friend[$val][0]))
                    $friend[$val] = 'Имя не укзано';
                else if(empty($friend[$val][0]))
                    $friend[$val] = '';
                else
                    $friend[$val] = $friend[$val][0];

            }
        }

        //Количество друзей
        $friends_count = MembersFriends::where('first_member_id', $member['id'])->orWhere('second_member_id', $member['id'])->count();
        return view('profile/friends/friends', ['type' => $type, 'friends' => $friends, 'friends_count' => $friends_count]);
    }

    public function getMembersFriendsSend(Request $request)
    {
        $member = $this->getAuthorizedMember();
        //Опредиление типа
        $type = 'all_members';
        if ($request->has('friends'))
            $type = 'friends';


        //Получение айдишников всех друзей
        $friends_ids = $this->getMemberFriendsArray($member['id']);

        if ($type == 'friends') {
            $friends = Members::whereIn('id', $friends_ids)->paginate(15);
        } else {
            $names = [];
            if($request->input('friend_search'))
                $names = explode(' ', $request->input('friend_search'));

            $members_ids_array = [];
            foreach($names as $name){
                 $members_ids = MembersInformation::where('field_name', 'name')->where('value', 'like', '%'.$name.'%')
                     ->orWhere('field_name', 'l_name')->where('value', 'like', '%'.$name.'%')->pluck('member_id')->toArray();
                 $members_ids_array = array_merge($members_ids, $members_ids_array);
            }
            $members_ids_array = array_unique($members_ids_array);

            if(!empty($names))
                $friends = Members::where('id', '!=', $member['id'])->whereIn('id', $members_ids_array)->paginate(15);
            else
                $friends = Members::where('id', '!=', $member['id'])->paginate(15);
        }

        foreach ($friends as $friend) {
            $friend['is_friend'] = false;

            if (in_array($friend['id'], $friends_ids))
                $friend['is_friend'] = true;

            $values = ['name', 'l_name', 'whos_i', 'profile_img'];
            foreach($values as $val){
                $friend[$val] = MembersInformation::where('public', 1)->where('member_id', $friend['id'])->where('field_name', $val)->pluck('value');
                if($val == 'name' && empty($friend[$val][0]))
                    $friend[$val] = 'Имя не укзано';
                else if(empty($friend[$val][0]))
                    $friend[$val] = '';
                else
                    $friend[$val] = $friend[$val][0];

            }
        }

        $friends_count = MembersFriends::where('first_member_id', $member['id'])->orWhere('second_member_id', $member['id'])->count();
        return view('profile/friends/friends', ['name_input' => $request->input('friend_search'),'type' => $type, 'friends' => $friends, 'friends_count' => $friends_count]);
    }

    //Метод для страницы, авторизации пользователя
    public function memberAuth(Request $request)
    {
        $step = 1;
        $email = $request->input('email');
        $phone = $request->input('phone');
        $errorMessages = [];
        $type = '';

        if ($request->has('password'))
            $step = 2;

        if ($request->has('first_password'))//если создаем новый пароль
            $step = 3;

        if ($step == 1) {
            if ($request->has('type')) {
                $type = $request->input('type');
            } else {
                if ($request->input('email')) {
                    $type = 'email';

                    if (!$email)
                        $errorMessages[] = 'Введите почту!';
                    else {
                        $member = Members::where('email', $email)->first();
                        if (!empty($member)) {
                            $step = 2;
                            if (!$member['password_changed'])
                                $step = 3;

                            if ($member['dev_key']) {
                                setcookie('hash', $member['dev_key'], (time() + (60 * 60 * 24 * 31 * 3)), '/');
                            }
                        } else {
                            $errorMessages[] = 'Пользователь с данной почтой не был найден';
                        }
                    }
                } elseif ($request->input('phone')) {
                    $type = 'phone';
                    $phone = '+' . preg_replace("/[^0-9]/", '', $phone);

                    if (!$phone)
                        $errorMessages[] = 'Введите номер телефона!';
                    else {
                        $member = Members::where('phone', $phone)->first();
                        if (!empty($member)) {
                            $step = 2;
                            if (!$member['password_changed'])
                                $step = 3;

                            if ($member['dev_key']) {
                                setcookie('hash', $member['dev_key'], (time() + (60 * 60 * 24 * 31 * 3)), '/');
                            }
                        } else {
                            $errorMessages[] = 'Пользователь с данным номером не был найден';
                        }
                    }
                }
            }
        } elseif ($step == 2 || $step == 3) {
            $dev_key = $_COOKIE['hash'];
            $member = Members::where('dev_key', $dev_key)->first();
            if (!empty($member)) {
                if ($step == 2) {
                    $password = md5($request->input('password'));

                    if ($password == $member['password']) {
                        $this->auth($member);
                        return redirect('/');
                    } else {
                        $errorMessages[] = 'Не верный пароль!';
                    }
                } elseif ($step == 3) {
                    $password_f = $request->input('first_password');
                    $password_s = $request->input('second_password');

                    if (strlen($password_f) < 6 || strlen($password_s) < 6)
                        $errorMessages[] = 'Пароли не могут быть меньше 6 символов!';
                    elseif ($password_f == $password_s) {
                        $member->update(['password_changed' => 1, 'password' => md5($password_f)]);
                        $member->save();

                        $this->auth($member);
                        return redirect('/');
                    } else {
                        $errorMessages[] = 'Пароли не совпадают!';
                    }
                }
            } else {
                $errorMessages[] = 'Произошла ошибка, попробуйте войти снова';
            }
        }
        //$errorMessages[] = 'Для авторизации нужно ввести email или номер телефона';
        return view('profile/auth', ['error_messages' => $errorMessages, 'type' => $type, 'step' => $step, 'email' => $email, 'phone' => $phone]);
    }
}
