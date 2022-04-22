@extends('layouts.app_admin')


@section('title-block')
    Admin - модули
@endsection

@section('content')
    <div class="container">
        <h1 class="mt-5 text-center">@if($type == 'edit')Редактирование@elseДобавление@endif модуля</h1>
        <hr>

        @if(isset($error_messages))
            @foreach($error_messages as $error)
                <div class="alert alert-warning">
                    {{ $error }}
                </div>
            @endforeach
        @endif

        <form action="{{ route('module-add-update-submit', $id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Название</label>
                <input type="text" class="form-control" id="name" name="name" @if(isset($data['name']) && $data['name'])value="{{$data['name']}}"@endif>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea class="form-control" id="description" rows="3" name="description">@if(isset($data['description']) && $data['description']){{$data['description']}}@endif</textarea>
            </div>
            <hr>
            @if(!isset($data['name']))
                <h6>Тарифы добавить в модуль, можно будет в его редактировании, после создания модуля</h6>
            @else
                <h6 class="text-center">Тарифы модуля</h6>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <ul class="list-group list-tariffes">
                            @foreach($module_tariffes as $tariff)
                                <li class="list-group-item"><span class="float-start">{{$tariff->name}}<br><small>{{$tariff->date_open_ru}}</small></span><button type="button" class="btn btn-sm float-end btnDeleteTariff" id="{{$tariff->tariff_id}}">X</button></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="list-group">
                            <select name="tariff_select" class="form-select mt-2" id="tariff_select">
                                @foreach($tariffes as $tariff)
                                    <option value="{{$tariff->id}}">{{$tariff->name}}</option>
                                @endforeach
                            </select>
                            <input type="datetime-local"  class="form-group mt-2" id="tariff_module_date">
                            <button type="button" class="btn btn-outline-success mt-2 btnAddTariff" @if(!isset($tariffes[0]) && empty($tariffes[0])) disabled @endif>Добавить тарифф</button>
                        </div>
                    </div>
                </div>
            @endif
            <button type="submit" class="btn btn-outline-success mt-2 btnSaveChanges" @if(isset($data['id'])) id="{{$data['id']}}" @endif>Сохранить</button>
        </form>
    </div>

    <script>
        $('.btnAddTariff').on('click', function(){
            let tariffId = $('#tariff_select').val();
            let moduleId = $('.btnSaveChanges').attr('id');
            let date = $('#tariff_module_date').val();

            if(!date){
                alert('Укажите дату!');
            }else{
                $.ajax({
                    url: "/admin/module-add-delete-tariff",
                    type: "POST",
                    data: {
                        _token: _token,
                        tariff_id: tariffId,
                        module_id: moduleId,
                        date: date,
                        type: 'add'
                    },
                    success: function(response){
                        console.log(response);
                        if(response['status'] == 'ok')
                            location.reload();
                        else
                            alert(response['message']);
                    },
                    error: function (error) {
                        alert('Произошла ошибка... Попробуйте перезапустить страницу')
                        console.log(error);
                    }
                });
            }
        })

        $('.btnDeleteTariff').on('click', function(){
            let tariffId = $(this).attr('id');
            let moduleId = $('.btnSaveChanges').attr('id');

            $.ajax({
                url: "/admin/module-add-delete-tariff",
                type: "POST",
                data: {
                    _token: _token,
                    tariff_id: tariffId,
                    module_id: moduleId,
                    type: 'delete'
                },
                success: function(response){
                    console.log(response);
                    if(response['status'] == 'ok')
                        location.reload();
                    else
                        alert(response['message']);
                },
                error: function (error) {
                    alert('Произошла ошибка... Попробуйте перезапустить страницу')
                    console.log(error);
                }
            });
        })
    </script>
@endsection