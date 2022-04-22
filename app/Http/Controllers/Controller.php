<?php

namespace App\Http\Controllers;

use App\Models\Lessons;
use App\Models\LessonTariff;
use App\Models\Members;
use App\Models\MembersInformation;
use DateTime;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //Получить авторизованного пользователя
    public function getAuthorizedMember()
    {
        $hash = $_COOKIE['auth_hash'];
        if ($hash) {
            $member = Members::where('auth_hash', $hash)->first();
            if (!empty($member))
                return $member;
        }
        return false;
    }

    public function getNextLessonId ($tariff_id, $idNext, $nextPrev = 'next') {
        $lt = LessonTariff::where('tariff_id', $tariff_id)->where('lesson_id', $idNext)->first();
        if (!empty($lt)) {
            return $lt['lesson_id'];
        } else {
            $l = Lessons::find($idNext);
            if (!empty($l)) {
                if ($nextPrev == 'next') {
                    return $this->getNextLessonId($tariff_id, $l['next_id'], 'next');
                }
                if ($nextPrev == 'prev') {
                    return $this->getNextLessonId($tariff_id, $l['prev_id'], 'prev');
                }
            }
        }
    }

    public function getAuthorizedMemberInformation($member_id)
    {
        $member_information = MembersInformation::where('member_id', $member_id)->get();

        $values = [];
        foreach ($member_information as $member_i) {
            $values[$member_i['field_name']] = $member_i['value'];
            if ($member_i['public'])
                $values['public_' . $member_i['field_name']] = true;
        }
        return $values;
    }

    public function embalishDate($date, $type = 'standard')
    {
        $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

        $startTime = new Datetime($date);
        if ($type == 'standard') {
            return (int)$startTime->format('d') . "-го " . $months[(int)$startTime->format('m') - 1] . ' ' . $startTime->format('Y') . ' в ' . $startTime->format('H') . ':' . $startTime->format('m');
        } elseif ($type == 'message') {
            return (int)$startTime->format('d') . " " . $months[(int)$startTime->format('m') - 1] . ' в ' . $startTime->format('H') . ':' . $startTime->format('m');

        }
    }

    public function makeHash($data)
    {//for dev-key
        return dechex(rand(1000, 9999)) . '-' . dechex($data);
    }

    public function makeAuthHash($member)
    {//for auth_hash
        return md5($member['id'] . '-' . date('Y-m-d H:i:s') . rand(10, 200));
    }
}
