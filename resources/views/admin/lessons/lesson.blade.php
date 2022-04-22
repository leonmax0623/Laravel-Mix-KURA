@extends('layouts.app_admin')

@section('title-block')
    Admin - уроки
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <h1 class="mt-5 text-center">Урок - {{$data['name']}}</h1>
            <p class="text-disabled text-center">{{$data['module_name']}}</p>
            <hr>

            <div class="col-12 mb-3">
                <a class="btn btn-outline-danger btn-sm float-end m-1" href="#">Удалить</a>
                <a class="btn btn-outline-warning btn-sm float-end m-1" href="{{ route('lesson-add-update', $data['id']) }}">Редактировать</a>
            </div>
                <div class="col-md-6 col-sm-12 mt-1">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Описание</h5>
                            <p class="card-text">{{$data['description']}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 mt-1">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Файлы урока</h5>
                            @foreach($lesson_files as $lf)
                                @if(isset($data[$lf['name']][0]) && !empty($data[$lf['name']][0]))
                                    <p>{{$lf['name_ru']}}:</p>
                                    <ul>
                                    @foreach($data[$lf['name']] as $file)
                                        <li><a href="{{$file['file_url']}}">{{$file['file_url']}}</a></li>
                                    @endforeach
                                    </ul>
                                @else
                                    <p><em>{{$lf['name_ru']}} в этом уроке отсутствуют...</em></p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
        </div>

    </div>

@endsection