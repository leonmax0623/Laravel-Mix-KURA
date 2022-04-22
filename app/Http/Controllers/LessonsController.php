<?php

namespace App\Http\Controllers;

use App\Models\LessonFiles;
use App\Models\Lessons;
use App\Models\Module;
use Illuminate\Http\Request;

class LessonsController extends Controller
{
    public function getLessonModuleName($lesson)
    {
        $module_name = Module::where('id', $lesson['module_id'])->select('name')->first();
        $name = '';

        if (!empty($module_name))
            $name = $module_name['name'];
        return $name;
    }

    //Получить все уроки
    public function getAllLessons()
    {
        $lessons = Lessons::where('deleted', 0)->get();
        foreach ($lessons as $lesson) {
            $lesson['module_name'] = $this->getLessonModuleName($lesson);
        }
        //print_r($lessons);
        return view('admin/lessons/lessons', ['data' => $lessons]);
    }

    //Получить один урок (посмотреть подробнее)
    public function getOneLesson($id)
    {
        $lesson = Lessons::find($id);
        $lesson['module_name'] = $this->getLessonModuleName($lesson);
        $lesson['lesson_video'] = LessonFiles::where('lesson_id', $lesson['id'])->where('file_type', 'lesson_video')->get();
        $lesson['audio'] = LessonFiles::where('lesson_id', $lesson['id'])->where('file_type', 'audio')->get();
        $lesson['pdf'] = LessonFiles::where('lesson_id', $lesson['id'])->where('file_type', 'pdf')->get();
        $lesson['document'] = LessonFiles::where('lesson_id', $lesson['id'])->where('file_type', 'document')->get();

        $lesson_files = [];
        $lesson_files[] = ['name' => 'lesson_video', 'name_ru' => 'Видео'];
        $lesson_files[] = ['name' => 'audio', 'name_ru' => 'Аудио'];
        $lesson_files[] = ['name' => 'pdf', 'name_ru' => 'PDF'];
        $lesson_files[] = ['name' => 'document', 'name_ru' => 'Документы'];
        return view('admin/lessons/lesson', ['data' => $lesson, 'lesson_files' => $lesson_files]);
    }

    public function getModulesAndLessons()
    {
        //Получаем модули
        $modules = Module::where('deleted', 0)->orderby('id')->get();

        // Получаем список всех уроков
        $allLessonsArray = [];

        $allLessonsArray['Без модуля'] = [];

        $lessons = Lessons::where('module_id', 0)->orderby('id')->get();
        foreach ($lessons as $lesson) {
            $allLessonsArray['Без модуля'][] = $lesson;
        }

        foreach ($modules as $module) {
            if (!isset($allLessonsArray[$module['name']]) || !$allLessonsArray[$module['name']]) {
                $allLessonsArray[$module['name']] = [];
            }

            $lessons = Lessons::where('module_id', $module['id'])->orderby('id')->get();
            foreach ($lessons as $lesson) {
                $allLessonsArray[$module['name']][] = $lesson;
            }
        }

        return ['modules' => $modules, 'all_lessons_array' => $allLessonsArray];
    }

    public function addUpdateLesson($id)
    {
        $type = 'edit';
        $data = '';
        $lesson_videos = [];

        if ($id == -1)
            $type = 'add';

        if ($type == 'edit') {
            $data = Lessons::find($id);

            //Получаем файлы урока (пока только видео)
            $lesson_videos = LessonFiles::where('lesson_id', $id)->where('file_type', 'lesson_video')->orderby('sort')->get();
        }

        $modules_and_lessons = $this->getModulesAndLessons();
        return view('admin/lessons/add-update-lesson', ['type' => $type, 'data' => $data, 'id' => $id, 'modules' => $modules_and_lessons['modules'], 'lesson_videos' => $lesson_videos, 'all_lessons_array' => $modules_and_lessons['all_lessons_array']]);
    }

    public function addUpdateLessonSubmit($id, Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'module_id' => $request->input('lesson_module'),
            'prev_id' => $request->input('prev_id'),
            'next_id' => $request->input('next_id'),
            'homework' => $request->input('homework'),
            'lesson_videos' => trim($request->input('lesson_videos')),
            'deleted' => 0
        ];

        $errorMessages = [];

        $type = 'edit';
        if ($id == -1)
            $type = 'add';

        $lessonCheckName = Lessons::where('deleted', 0)->where('name', $data['name'])->where('module_id', $data['module_id'])->where('id', '!=', $id)->first();
        if (!empty($moduleCheckName)) {
            $errorMessages[] = 'Урок в данном модуле, с указанным названием уже существует!';
        } elseif (!$data['name'] || !$data['description'] || !$data['module_id']) {
            $errorMessages[] = 'Не все обязательные поля были указаны!';
        } elseif ($data['prev_id'] == $id || $data['next_id'] == $id) {
            $errorMessages[] = 'Следующий/предыдущий урок не может быть текущим';
        } else {
            $lesson = new Lessons();
            if ($type == 'edit') {
                $data['id'] = $id;

                $lesson = Lessons::find($id);
                $lesson->update([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'module_id' => $data['module_id'],
                    'prev_id' => $data['prev_id'],
                    'next_id' => $data['next_id'],
                    'homework' => $data['homework']
                ]);

                //Обновление файлов
                $lesson_videos = LessonFiles::where('lesson_id', $id)->where('file_type', 'lesson_video')->orderby('sort')->get();
                $lesson_videos_text = '';
                foreach ($lesson_videos as $lv) {
                    $lesson_videos_text .= $lv['file_url'] . "\n";
                }

                if (!$data['lesson_videos'])
                    $data['lesson_videos'] = null;

                if ($lesson_videos_text != $data['lesson_videos']) {
                    foreach ($lesson_videos as $lv)
                        $lv->delete();

                    if ($data['lesson_videos']) {
                        $lesson_videos_text = explode("\n", $data['lesson_videos']);
                        $i = 0;
                        foreach ($lesson_videos_text as $lvt) {
                            $lesson_videofile = new LessonFiles([
                                'lesson_id' => $id,
                                'file_url' => $lvt,
                                'file_type' => 'lesson_video',
                                'sort' => $i
                            ]);
                            $lesson_videofile->save();
                            $i++;
                        }
                    }
                }

            } else {
                $lesson = new Lessons([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'module_id' => $data['module_id'],
                    'prev_id' => $data['prev_id'],
                    'next_id' => $data['next_id'],
                    'homework' => $data['homework']
                ]);
            }
            $lesson->save();

            if ($type == 'add')
                return redirect('/admin/lessons');
        }

        //Получаем файлы урока (пока только видео)
        $lesson_videos = LessonFiles::where('lesson_id', $id)->where('file_type', 'lesson_video')->orderby('sort')->get();
        $modules_and_lessons = $this->getModulesAndLessons();
        return view('admin/lessons/add-update-lesson', ['error_messages' => $errorMessages, 'type' => $type, 'data' => $data, 'id' => $id, 'modules' => $modules_and_lessons['modules'], 'lesson_videos' => $lesson_videos, 'all_lessons_array' => $modules_and_lessons['all_lessons_array']]);
    }

}
