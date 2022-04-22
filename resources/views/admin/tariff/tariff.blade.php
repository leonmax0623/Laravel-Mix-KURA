@extends('layouts.app_admin')


@section('title-block')
    Admin - тарифы
@endsection

@section('content')
    <div class="container">
        <h1 class="mt-5 text-center">Тарифы</h1>
        <hr>

        <div class="tbl-container table-responsive-sm"> <!-- <== overflow: hidden applied to parent -->
            <table class="table table-hover">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                    <th scope="col">Дата начала</th>
                    <th scope="col">Дата окончания</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                    @if(!isset($data[0]) && empty($data[0]))
                        <tr>
                            <th scope="row">0</th>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endif

                    @foreach($data as $element)
                        <tr>
                            <th scope="row">{{$element['id']}}</th>
                            <td>{{$element['name']}}</td>
                            <td>{{$element['date_open_ru']}}</td>
                            <td>{{$element['date_close_ru']}}</td>
                            <td>
                                <a class="btn btn-outline-warning btn-sm btnEditTariff" href="{{ route('tariff-add-update', $element['id']) }}"><i class="fa-solid fa-pen"></i></a>
                                &nbsp;
                                <button class="btn btn-outline-danger btn-sm btnDeleteTariff" id="{{$element['id']}}"><i class="fa-solid fa-trash-can"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a class="btn btn-outline-success btn-sm float-end" href="{{ route('tariff-add-update', -1) }}">Добавить тариф</a>
    </div>

    <script>
        $('.btnDeleteTariff').on('click', function(){
            let tariff_id = $(this).attr('id');

            $.ajax({
                url: "/admin/tariff-delete",
                type: "POST",
                data: {
                    id: tariff_id,
                    _token: _token
                },
                success: function(){
                    location.reload();
                },
                error: function (error) {
                    alert('Произошла ошибка... Попробуйте перезапустить страницу')
                    console.log(error);
                }
            });
        })
    </script>
@endsection