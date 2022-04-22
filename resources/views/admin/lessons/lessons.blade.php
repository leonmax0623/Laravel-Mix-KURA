@extends('layouts.app_admin')

@section('script-header')
    <link rel="stylesheet" href="{{ asset('/css/visible_scrollbar.css') }}">
@endsection

@section('title-block')
    Admin - уроки
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <h1 class="mt-5 text-center">Уроки</h1>
            <hr>

            <div class="col-12 mb-3">
                <a class="btn btn-outline-success btn-sm float-end" href="{{ route('lesson-add-update', -1) }}">Добавить урок</a>
            </div>
            @foreach($data as $element)
                <div class="col-md-3 col-sm-12 mb-1">
                    <div class="card ">
                        <div class="card-body">
                            <h5 class="card-title">{{$element['name']}}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $element['module_name'] }}</h6>
                            <p class="card-text">{{mb_strimwidth($element['description'], 0, 250, "...")}}</p>
                            <a href="{{ route('admin-lesson', $element['id']) }}" class="card-link btn btn-outline-info float-end">Подробнее</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

@endsection