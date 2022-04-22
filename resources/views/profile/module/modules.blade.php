@extends('layouts.app_profile')

@section('title-block')
    Обучение
@endsection

@section('content')
    <div class="container">
        <h1 class="mt-5">Обучение</h1>
        <div class="row">
            <div class="col-md-9 col-sm-12 mt-2">
                @php $i = 1; @endphp
                @foreach($modules as $module)
                    <div class="row border p-3 @if($i != 3) mt-3 @endif">
                        <div class="@if($i == 1) col-md-6 @else col-md-2 @endif bg-white">
                            <img src="@if($module->img_url) {{$module->img_url}} @else /public/system/no-image.png @endif" class="rounded" @if($i == 1) style="width: 250px" @else style="width: 125px" @endif>
                        </div>
                        @if($i == 1)
                            <div class="col-md-6 bg-white">
                                <small>Модуль {{$i}}</small>
                                <h1>{{$module->name}}</h1>
                                <p>{{$module->description}}</p>
                                <a href="@if($module->opened == 1){{ route('profile-lessons', $module->id) }} @else # @endif" class="col btn @if($module->opened == 1) btn-outline-secondary @else btn-secondary @endif col-12 p-3 text-center">@if($module->opened == 1) Перейти @elseif($module->opened == 0) Модуль закрыт @else В вашем тарифе, данный модуль не доступен @endif</a>
                            </div>
                        @else
                            <div class="col-md-10 bg-white">
                                <div class="text-box float-start col-5">
                                    <small class="text-gray">{{$module->lessons_count}} уроков</small><br>
                                    <small>Модуль {{$i}}</small>
                                    <h3>{{$module->name}}</h3>
                                    @if($module->opened == 0)
                                        <small>Дата выхода: {{$module->date_open_ru}}</small>
                                    @endif
                                </div>

                                <div class="button-block float-end col-5">
                                    <a href="@if($module->opened == 1){{ route('profile-lessons', $module->id) }} @else # @endif" class="col btn @if($module->opened == 1) btn-outline-secondary @else btn-secondary @endif col-12 p-3 text-center" style="margin-top: 10%">@if($module->opened == 1) Перейти @elseif($module->opened == 0) Модуль закрыт @else В вашем тарифе, данный модуль не доступен @endif</a>
                                </div>
                            </div>
                        @endif
                    </div>

                    @php $i++; @endphp
                @endforeach
            </div>

            <div class="col-md-3 col-sm-12 mt-2">
                <h4 class="text-center">Расписание</h4>
            </div>
        </div>
        <br><br><br>
{{--        <pre>--}}
{{--            {{var_dump($data)}}--}}
{{--        </pre>--}}
    </div>
@endsection