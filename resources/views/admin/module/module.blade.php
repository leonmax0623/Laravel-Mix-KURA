@extends('layouts.app_admin')

@section('script-header')
    <link rel="stylesheet" href="{{ asset('/css/visible_scrollbar.css') }}">
@endsection

@section('title-block')
    Admin - модули
@endsection

@section('content')
    <div class="container">
        <h1 class="mt-5 text-center">Модули</h1>
        <hr>

        <div class="tbl-container table-responsive-sm">
            <table class="table table-hover">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                    <th scope="col" style="width: 800px;">Описание</th>
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
                        </tr>
                    @endif

                    @foreach($data as $element)
                        <tr>
                            <th scope="row">{{$element['id']}}</th>
                            <td>{{$element['name']}}</td>
                            <td>
                                <div style="max-height: 150px; overflow: auto;">
                                    {{$element['description']}}
                                </div>
                            </td>
                            <td>
                                <a class="btn btn-outline-warning btn-sm" href="{{ route('module-add-update', $element['id']) }}"><i class="fa-solid fa-pen"></i></a>
                                &nbsp;
                                <button class="btn btn-outline-danger btn-sm btnDeleteModule" id="{{$element['id']}}"><i class="fa-solid fa-trash-can"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a class="btn btn-outline-success btn-sm float-end" href="{{ route('module-add-update', -1) }}">Добавить модуль</a>

    </div>

    <script>
        $('.btnDeleteModule').on('click', function(){
            let module_id = $(this).attr('id');

            $.ajax({
                url: "/admin/module-delete",
                type: "POST",
                data: {
                    id: module_id,
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