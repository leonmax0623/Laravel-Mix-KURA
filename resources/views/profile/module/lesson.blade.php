@extends('layouts.app_profile')

@section('title-block')
    Обучение
@endsection

@section('content')
    <div class="container">
        @if(!$lesson_open)
            <div class="text-center">
                <h1 class="mt-5">Урок не доступен</h1>
            </div>
        @else
            <h1 class="mt-5">Обучение</h1>
            <div class="row">
                Урок открыт!)
            </div>
            <br><br><br>
            {{var_dump($lesson)}}
        @endif

    </div>
@endsection