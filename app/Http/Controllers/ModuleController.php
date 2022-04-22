<?php

namespace App\Http\Controllers;

use App\Models\Lessons;
use App\Models\LessonTariff;
use App\Models\Module;
use App\Models\ModuleTariff;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    // ADMIN ---
    //Получить все модули
    public function getAllModule()
    {
        return view('admin/module/module', ['data' => Module::where('deleted', 0)->get()]);
    }

    //Удаление модуля по его ID
    public function deleteModule(Request $request)
    {
        $module = Module::find($request->id)->update(['deleted' => 1]);

        return response()->json(['status' => 'ok']);
    }

    //Добавление/редактирование модулей
    public function addUpdateModule($id)
    {
        $type = 'edit';
        $data = '';
        $module_tariffes = '';
        $tariffes = [];

        if ($id == -1)
            $type = 'add';

        if ($type == 'edit') {
            $data = Module::find($id);
            $module_tariffes = DB::table('module_tariff')
                ->where('module_tariff.module_id', $id)
                ->join('tariff', 'module_tariff.tariff_id', '=', 'tariff.id')
                ->where('tariff.deleted', 0)
                ->get();
            foreach($module_tariffes as $mt){
                $mt->date_open_ru = $this->embalishDate($mt->date_open, 'message');
            }

            $m_tariffes = ModuleTariff::where('module_id', $id)->pluck('tariff_id')->toArray();
            $tariffes = Tariff::where('deleted', 0)->whereNotIn('id', $m_tariffes)->get();
        }
        return view('admin/module/add-update-module', ['type' => $type, 'data' => $data, 'id' => $id, 'module_tariffes' => $module_tariffes, 'tariffes' => $tariffes]);
    }

    public function addDeleteTariffModule(Request $request){
        $module_id = $request->input('module_id');
        $tariff_id = $request->input('tariff_id');
        $type = $request->input('type');

        if (!$module_id || !$tariff_id) {
            return response()->json(['status' => 'error', 'data' => '', 'message' => 'Произошла ошибка, попробуйте перезапустить страницу', 'error_code' => 0]);
        }


        $moduleTariff = ModuleTariff::where('module_id', $module_id)->where('tariff_id', $tariff_id)->first();
        if (!empty($moduleTariff)) {
            if($type == 'delete') {
                $moduleTariff->delete();
                return response()->json(['status' => 'ok', 'data' => '', 'message' => '', 'error_code' => -1]);
            }elseif($type == 'add') {
                return response()->json(['status' => 'error', 'data' => '', 'message' => 'Выбраный тариф уже добавлени в данный модуль', 'error_code' => 0]);
            }
        }else {
            if($type == 'add') {
                $date = $request->input('date');
                if(!$date){
                    return response()->json(['status' => 'error', 'data' => '', 'message' => 'Укажите дату!', 'error_code' => 1]);
                }else {
                    $moduleTariff = new ModuleTariff([
                        'tariff_id' => $tariff_id,
                        'module_id' => $module_id,
                        'date_open' => $date
                    ]);
                    $moduleTariff->save();
                    return response()->json(['status' => 'ok', 'data' => '', 'message' => '', 'error_code' => -1]);
                }
            }
        }

        return response()->json(['status' => 'error', 'data' => '', 'message' => 'Произошла ошибка, попробуйте перезапустить страницу', 'error_code' => 0]);
    }

    public function addUpdateModuleSubmit($id, Request $request)
    {
        $name = $request->input('name');
        $description = $request->input('description');
        $errorMessages = [];
        $data = [];

        $type = 'edit';
        if ($id == -1)
            $type = 'add';

        if ($type == 'add') {
            $data['name'] = $name;
            $data['description'] = $description;
        }

        $moduleCheckName = Module::where('deleted', 0)->where('name', $name)->where('id', '!=', $id)->first();
        if (!empty($moduleCheckName)) {
            $errorMessages[] = 'Модуль с данным назваением уже существует!';
        } elseif (!$name || !$description) {
            $errorMessages[] = 'Не все поля были указаны!';
        } else {
            $module = new Module;
            if ($type == 'edit') {
                $module = Module::find($id);
                $module->update([
                    'name' => $name,
                    'description' => $description
                ]);
                $data = $module;
            } else {
                $module = new Module([
                    'name' => $name,
                    'description' => $description
                ]);
            }
            $module->save();

            if ($type == 'add')
                return redirect('/admin/module');
        }

        if ($type == 'edit' && !empty($errorMessages))
            $data = Module::find($id);

        return view('admin/module/add-update-module', ['type' => $type, 'data' => $data, 'id' => $id, 'error_messages' => $errorMessages]);
    }

    // MEMBER ---
    public function getAllMemberModules(){
        $member = $this->getAuthorizedMember();
        $tariff = $member['tariff_id'];

        $modules = Module::where('deleted', 0)->get();
//        $modules = DB::table('module_tariff')
//            ->where('module_tariff.tariff_id', $tariff)
//            ->join('module', 'module.id', '=', 'module_tariff.module_id')
//            ->where('module.deleted', 0)
//            ->orderBy('module_tariff.date_open')
//            ->get();

        foreach($modules as $module){
            $module_tariff_check = ModuleTariff::where('module_id', $module['id'])->where('tariff_id', $tariff)->first();
            $module['opened'] = -1;//Модуль не доступен, из-за тарифа
            $module['date_open_ru'] = '';

            if(!empty($module_tariff_check)){
                $module['opened'] = 0;//Модуль не открыт, из-за даты
                $module['date_open_ru'] = $this->embalishDate($module_tariff_check['date_open'], 'message');

                if($module_tariff_check['date_open'] < date('Y-m-d H:i:s'))
                    $module['opened'] = 1;// Модуль полностью доступен

            }

            $module['lessons_count'] = $this->getModuleLessons($tariff, $module['module_id'], true);
        }

        return view('profile/module/modules', ['modules' => $modules]);
    }

    public function getOneProfileLesson($lesson_id){
        $member = $this->getAuthorizedMember();
        $tariff_id = $member['tariff_id'];
        $lesson_open = false;

        $lesson = Lessons::find($lesson_id);
        if(!empty($lesson)) {
            $module = ModuleTariff::where('tariff_id', $tariff_id)->where('module_id', $lesson['module_id'])->first();
            if(!empty($module) && date('Y-m-d H:i:s') > $module['date_open']){
                $lesson_tariff = LessonTariff::where('lesson_id', $lesson['id'])->where('tariff_id', $tariff_id)->first();
                if(!empty($lesson_tariff)){
                    $lesson_open = true;
                }
            }
        }

        return view('profile/module/lesson', ['lesson_open' => $lesson_open, 'lesson' => $lesson]);
    }

    public function getOneModule($module_id){
        $member = $this->getAuthorizedMember();
        $tariff = $member['tariff_id'];

        $module = DB::table('module')
            ->where('module.id', $module_id)
            ->join('module_tariff', 'module.id', '=', 'module_tariff.module_id')
            ->where('module.deleted', 0)
            ->where('module_tariff.tariff_id', $tariff)
            ->first();
        if(empty($module)){
            return view('profile/module/lessons', ['module_open' => false]);
        }else{
            $lessons = $this->getModuleLessons($tariff, $module->module_id);
            if($module->date_open > date('Y-m-d H:i:s'))
                return view('profile/module/lessons', ['module_open' => false, 'date_open' => $this->embalishDate($module->date_open, 'message')]);

            return view('profile/module/lessons', ['module_open' => true, 'lessons' => $lessons]);
        }
    }

    public function getModuleLessons($tariff_id, $module_id, $count = false){
        $lessons = Lessons::where('module_id', $module_id)->where('deleted', 0)->orderby('id');
        if($count)
            $lessons = $lessons->count();
        else {
            $lessons = $lessons->get();
            foreach($lessons as $lesson){
                $lesson['opened'] = -1;//Урок закрыт из-за тарифа
                $lesson_tariff = LessonTariff::where('lesson_id', $lesson['id'])->where('tariff_id', $tariff_id)->first();
                if(!empty($lesson_tariff)) {
                    $lesson['opened'] = 1;

                    $prev_lesson_id = $this->getNextLessonId($tariff_id, $lesson['prev_id'], 'prev');
                    $lesson['prev_lesson_id'] = $prev_lesson_id;//для теста

                    //Здесь, если нужно будет, будет проверка домашки предыдущего урока - если домашка предыдущего урока не сдана, тогда этот закрыт - пока не будет пройдена домашка прошлого
                    if($prev_lesson_id != null){

                    }
                }
            }
        }

        return $lessons;
    }
}
