@extends('layouts.app_profile')

@section('title-block')
    Обучение
@endsection

@section('content')
    <div class="container">
        @if(!$module_open)
            <div class="text-center">
                <h1 class="mt-5">Модуль не доступен</h1>
                @if(isset($date_open))
                    <p>До {{$date_open}}</p>
                @endif
            </div>
        @else
            <h1 class="mt-5">Обучение</h1>
            <div class="row">
                @php $i = 1; @endphp
                @foreach($lessons as $lesson)
                    <div class="col-md-12 border p-3 mt-3">
                        <div class="bg-white @if($lesson->opened == -1) text-secondary @endif">
                            <div class="text-box float-start col-7">
                                <small>Урок {{$i}}</small>
                                <h3>{{$lesson->name}}</h3>
                                <span>@if($lesson->opened == -1) В вашем тарифе, данный урок не доступен @elseif($lesson->opened == 1)  @else Открыт @endif</span>
                            </div>

                            @if($lesson->opened == 1)
                                <div class="button-block float-end col-3">
                                    <a href="{{ route('profile-lesson', $lesson->id) }}" class="col btn btn-outline-secondary col-12 p-3 text-center">Перейти</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @php $i++; @endphp
                @endforeach

            </div>
            <br><br><br>
        @endif

    </div>
@endsection