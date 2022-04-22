<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Members;
use App\Models\MembersInformation;
use Illuminate\Http\Request;

class MembersController extends Controller
{

    /**
     * @param $memberId
     * @param $key
     * @param $value
     * @param $public
     * @return void
     *
     * Дополнительные поля у пользователя
     */
    private function changeMemberField($memberId, $key, $value, $public = 0)
    {
        $member_info = new MembersInformation([
            'member_id' => $memberId,
            'field_name' => $key,
            'value' => $value,
            'public' => $public
        ]);
        $member_info->save();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Регистрация пользователя
     */
    public function registerMember(Request $request)
    {
        $tariff = $request->get('tariff');

        $email = $request->get('email');
        $role = $request->get('role');
        if (!$role) {
            $role = 'student';
        }
        $phone = $request->get('phone');

        //Не обязательные поля
        $name = $request->get('name');

        if ($tariff && $email && $role && $phone) {
            $member = Members::where('email', $email)->first();
            if (empty($member)) {
                try {
                    $member = new Members([
                        'email' => $email,
                        'phone' => '+'.$phone,
                        'password_changed' => 0,
                        'tariff_id' => $tariff,
                        'role' => $role,
                        'password' => 'none',
                        'dev_key' => 'none'
                    ]);
                    $member->save();

                    $member->update(['dev_key' => $this->makeHash($member['id'])]);
                    $member->save();

                    if ($name) {
                        $this->changeMemberField($member['id'], 'name', $name, true);
                    }
                } catch (\Exception $ex) {
                    return response()->json(['status' => 'error', 'data' => '', 'error_code' => 2, 'message' => $ex->getMessage()]);
                }
            } else {
                return response()->json(['status' => 'error', 'data' => '', 'error_code' => 1, 'message' => 'Member with this email, is already exist!']);
            }
        } else {
            return response()->json(['status' => 'error', 'data' => '', 'error_code' => 0, 'message' => 'Not all fields were filled']);
        }
        return response()->json(['status' => 'ok', 'data' => 'nice', 'error_code' => -1, 'message' => '']);
    }
}
