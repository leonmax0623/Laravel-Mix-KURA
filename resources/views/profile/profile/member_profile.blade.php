@extends('layouts.app_profile')

@section('title-block')
    Личный кабинет пользователя
@endsection

@section('content')
    <div class="container">
        <h1>Some text I write</h1>
        <pre>
            {{$data}}
        </pre>
        <br><br><br>
    </div>
@endsection