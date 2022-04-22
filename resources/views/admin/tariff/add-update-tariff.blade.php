@extends('layouts.app_admin')


@section('title-block')
    Admin - тарифы
@endsection

@section('content')
    <div class="container">
        <h1 class="mt-5 text-center">@if($type == 'edit')Редактирование@elseДобавление@endif тарифа</h1>
        <hr>

        @if(isset($error_messages))
            @foreach($error_messages as $error)
                <div class="alert alert-warning">
                    {{ $error }}
                </div>
            @endforeach
        @endif

        <form action="{{ route('tariff-add-update-submit', $id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Название</label>
                <input type="text" class="form-control" id="name" name="name" @if(isset($data['name']) && $data['name'])value="{{$data['name']}}"@endif>
            </div>

            <div class="mb-3">
                <label for="date_open" class="form-label">Дата старта</label>
                <input type="datetime-local" class="form-control" id="date_open" name="date_open" @if(isset($data['date_open']) && $data['date_open'])value="{{date('Y-m-d\TH:i', strtotime($data['date_open']))}}"@endif>
            </div>

            <div class="mb-3">
                <label for="date_close" class="form-label">Дата окончания</label>
                <input type="datetime-local" class="form-control" id="date_close" name="date_close" @if(isset($data['date_close']) && $data['date_close'])value="{{date('Y-m-d\TH:i', strtotime($data['date_close']))}}"@endif>
            </div>

            <button type="submit" class="btn btn-outline-success">Сохранить</button>
        </form>
    </div>
@endsection