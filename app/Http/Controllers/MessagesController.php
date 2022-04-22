<?php

namespace App\Http\Controllers;

use App\Models\Members;
use App\Models\MembersInformation;
use App\Models\Messages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MessagesController extends Controller
{
    public function getMembersForCorrespondence($members_ids)
    {
        $membersCorrespondence = Members::whereIn('id', $members_ids)->get();
        foreach ($membersCorrespondence as $memberCorrespondence) {

            $memberInfo = MembersInformation::where('member_id', $memberCorrespondence['id'])->where('field_name', 'name')->first();
            if (!empty($memberInfo))
                $memberCorrespondence['name'] = $memberInfo['value'];
            else
                $memberCorrespondence['name'] = 'Имя не указано';

            $memberInfo = MembersInformation::where('member_id', $memberCorrespondence['id'])->where('field_name', 'profile_img')->orderby('id', 'desc')->first();
            if (!empty($memberInfo))
                $memberCorrespondence['profile_img'] = $memberInfo['value'];
            else
                $memberCorrespondence['profile_img'] = '/public/system/no-image.png';
        }

        return $membersCorrespondence;
    }

    //Получаем все чаты
    public function getUserCorrespondence($member_id)
    {
        //echo "<h1>".$member_id."</h1>";
        $correspondence_array = [];
        $messages = Messages::where('from_id', $member_id)->where('deleted', 0)
            ->orWhere('to_id', $member_id)->where('deleted', 0)
            ->get();

        foreach ($messages as $message) {
            if ($message['from_id'] == $member_id && !in_array($message['to_id'], $correspondence_array))
                $correspondence_array[] = $message['to_id'];
            elseif ($message['to_id'] == $member_id && !in_array($message['from_id'], $correspondence_array))
                $correspondence_array[] = $message['from_id'];
        }

        return $correspondence_array;
    }

    public function sendMessage(Request $request)
    {
        $text = strip_tags($request->input('text'));
        $member_to = $request->input('member_dev_key');
        $parent_id = $request->input('parent_id');
        $parent_text = '';
        $member_from = $this->getAuthorizedMember();
        $text_message_id = 0;

        if ($member_to != $member_from['dev_key']) {

            $member_to = Members::where('dev_key', $member_to)->first();
            if (!empty($member_to)) {
                $member_from_img = MembersInformation::where('member_id', $member_from['id'])->where('field_name', 'profile_img')->orderby('id', 'desc')->first();
                $member_from_img_path = '/public/system/no-image.png';
                if (!empty($member_from_img))
                    $member_from_img_path = $member_from_img['value'];

                $files = $_FILES;; // полученные файлы
                $path_array = [];

                if ($text) {
                    $message = new Messages([
                        'parent_id' => $parent_id,
                        'from_id' => $member_from['id'],
                        'to_id' => $member_to['id'],
                        'text' => $text,
                        'send_date' => date('Y-m-d H:i:s'),
                        'opened' => 0,
                        'message_type' => 'message'
                    ]);
                    $message->save();
                    $text_message_id = $message['id'];

                    if ($parent_id) {
                        $parent_text = Messages::find($parent_id);
                        $parent_text = $parent_text['text'];
                    }
                }

                if (!empty($files)) {

                    //@TODO тут должны быть все проверки безопасности передавемых файлов и вывести ошибки если нужно

                    foreach ($_FILES as $file) {

                        $filename = time() . '_' . $file['name'];
                        if (move_uploaded_file($file['tmp_name'], "messages_photos/$filename")) {
                            $path = url('/') . '/public/messages_photos/' . $filename;

                            $message = new Messages([
                                'parent_id' => $parent_id,
                                'from_id' => $member_from['id'],
                                'to_id' => $member_to['id'],
                                'text' => $path,
                                'send_date' => date('Y-m-d H:i:s'),
                                'opened' => 0,
                                'message_type' => 'file'
                            ]);
                            $message->save();
                            $path_array[] = $path;
                        }
                    }
                }

                if (!empty($path_array) || $text) {
                    return response()->json(['status' => 'ok', 'data' => ['text_message_id' => $text_message_id, 'parent_text' => $parent_text, 'member_img_path' => $member_from_img_path, 'text' => $text, 'path_array' => $path_array, 'send_date' => $this->embalishDate(date('Y-m-d H:i:s'), 'message')], 'message' => '', 'error_code' => -1]);
                }

                return response()->json(['status' => 'ok', 'data' => ['path_array' => '', 'member_img_path' => '', 'text' => '', 'send_date' => ''], 'message' => '', 'error_code' => -1]);

            } else {
                return response()->json(['status' => 'error', 'data' => '', 'message' => 'Пользователь не был найден...', 'error_code' => 2]);
            }
        } else {
            return response()->json(['status' => 'error', 'data' => '', 'message' => 'Вы не можете отправить сообщение самому себе!', 'error_code' => 1]);
        }
    }

    public function searchMember(Request $request)
    {
        try {
            $member_dev = $request->input('member_dev');
            $member_email = $request->input('member_email');

            $members = Members::where('email', 'like', '%' . $member_email . '%')->get();
            $membersIds = [];
            foreach ($members as $member) {
                $membersIds[] = $member['id'];
            }

            $membersCorrespondence = $this->getMembersForCorrespondence($membersIds);
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'data' => '', 'messsage' => 'Произошла ошибка...', 'error_code' => 0]);

        }
        return response()->json(['status' => 'ok', 'data' => $membersCorrespondence, 'messsage' => '', 'error_code' => -1]);
    }

    public function getOneMessage($member_dev)
    {
        $member = $this->getAuthorizedMember();
        $chat = [];

        if (!empty($member)) {
            $correspondence = $this->getUserCorrespondence($member['id']);

            if ($member_dev) {
                $member_conv = Members::where('dev_key', $member_dev)->first();
                if (!empty($member_conv)) {
                    $correspondence[] = $member_conv['id'];
                }
            }
            $membersCorrespondence = $this->getMembersForCorrespondence($correspondence);

            if ($member_dev && !empty($member_conv)) {
                $chat = Messages::where('from_id', $member_conv['id'])->where('to_id', $member['id'])->where('deleted', 0)
                    ->orWhere('from_id', $member['id'])->where('to_id', $member_conv['id'])->where('deleted', 0)
                    ->orderby('send_date')
                    ->get();

                foreach ($chat as $ch) {
                    if (!$ch['opened']) {
                        $ch->update(['opened' => 1, 'opened_date' => date('Y-m-d H:i:s')]);
                        $ch->save();
                    }


                    $ch['send_date'] = $this->embalishDate($ch['send_date'], 'message');
                    $ch['member_name'] = MembersInformation::where('member_id', $ch['from_id'])->where('field_name', 'name')->select('value')->first();
                    $ch['member_profile_img'] = MembersInformation::where('member_id', $ch['from_id'])->where('field_name', 'profile_img')->orderby('id', 'desc')->select('value')->first();

                    if (!empty($ch['member_name']))
                        $ch['member_name'] = $ch['member_name']['value'];
                    else
                        $ch['member_name'] = 'Имя не указано';

                    if (!empty($ch['member_profile_img']))
                        $ch['member_profile_img'] = $ch['member_profile_img']['value'];

                    if ($ch['parent_id']) {
                        $ch['parent_text'] = Messages::find($ch['parent_id']);
                        $ch['parent_text'] = $ch['parent_text']['text'];
                    }
                }
            }
        }

        return view('profile/messages', ['member_id' => $member['id'], 'members_correspondence' => $membersCorrespondence, 'current_member_dev' => $member_dev, 'chat' => $chat]);

    }
}
