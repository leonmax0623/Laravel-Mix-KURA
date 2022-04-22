@extends('layouts.app_admin')


@section('title-block')
    Admin - уроки
@endsection

@section('content')
    <div class="container">
        <h1 class="mt-5 text-center">@if($type == 'edit')Редактирование@elseДобавление@endif урока</h1>
        <hr>

        @if(isset($error_messages))
            @foreach($error_messages as $error)
                <div class="alert alert-warning">
                    {{ $error }}
                </div>
            @endforeach
        @endif

{{--        {{ route('lesson-add-update-submit', $id) }}--}}
        <form action="{{ route('lesson-add-update-submit', $id) }}" method="POST">
            @csrf

            <div class="row">
                <div class="mb-3 col-md-6 col-sm-12">
                    <label for="name" class="form-label">Название <strong>*</strong></label>
                    <input type="text" class="form-control" id="name" name="name" @if(isset($data['name']) && $data['name'])value="{{$data['name']}}"@endif>
                </div>
                <div class="mb-3 col-md-6 col-sm-12">
                    <label for="lesson_module" class="form-label">Модуль <strong>*</strong></label>
                    <select name="lesson_module" class="form-select" id="lesson_module">
                        @foreach($modules as $module)
                            <option value="{{$module['id']}}" @if(isset($data['module_id']) && $module['id'] == $data['module_id']) selected @endif>{{$module['name']}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 col-12">
                    <label for="description" class="form-label">Описание <strong>*</strong></label>
                    <textarea class="form-control" id="description" rows="3" name="description">@if(isset($data['description']) && $data['description']){{$data['description']}}@endif</textarea>
                </div>


                @foreach(['prev' => 'Предыдущий', 'next' => 'Следующий'] as $k => $n)
                <div class="mb-3 col-md-6 col-sm-12">
                    <label for="{{$k.'_id'}}">{{$n}} урок</label>
                    <select name="{{$k.'_id'}}" id="{{$k.'_id'}}" class="form-control">
                        <option value="0">--- Выберите {{$n}} урок---</option>
                        @foreach ($all_lessons_array as $module => $lessons)
                        <optgroup label="{{$module}}">
                            @foreach($lessons as $l)
                                <option value="{{$l['id']}}" @if(isset($data['id']) && $data['id'] == $l['id']) disabled @endif @if((isset($data['prev_id']) && $k == 'prev' && $data['prev_id'] == $l['id']) || (isset($data['next_id']) && $k == 'next' && $data['next_id'] == $l['id'])) selected @endif>{{$l['id']}} {{$l['name']}}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                @endforeach

                <div class="mb-3 col-12">
                    <label for="homework" class="form-label">Домашнее задание</label>
                    <textarea class="form-control" id="description" rows="3" name="homework">@if(isset($data['homework']) && $data['homework']){{$data['homework']}}@endif</textarea>
                </div>

                <hr>
                <h6 class="text-center">Файлы урока </h6>
                <small class="text-center">(пока можно добавить только ссылки на видео)</small>
                <div class="mb-3 col-sm-12 col-md-6">
                    <label for="lesson_videos" class="form-label">Ссылки на видео <strong>(разделять через Enter)</strong></label>
                    <textarea class="form-control" id="lesson_videos" rows="3" name="lesson_videos">@foreach($lesson_videos as $video){{$video['file_url']."\n"}}@endforeach</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-outline-success mb-5">Сохранить</button>
        </form>
    </div>
@endsection