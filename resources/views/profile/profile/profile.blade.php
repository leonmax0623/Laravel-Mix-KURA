@extends('layouts.app_profile')

@section('title-block')
    Личный кабинет
@endsection

@section('content')
    <div class="container">
        <form action="{{ route('profile-send') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-12 mt-3">
                    @if(isset($error_messages) && $error_messages)
                        @foreach($error_messages as $em)
                            <div class="col-12 mt-3 alert alert-warning">
                                {{$em}}
                            </div>
                        @endforeach
                    @endif

                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="card-title">Личная информация</h5>

                            <div class="form-group p-2">
                                <label for="name">Имя <strong style="color: red">*</strong></label>
                                <input type="text" class="form-control" id="name" placeholder="Введите имя" name="name" @if(isset($data['name'])) value="{{$data['name']}}" @endif>
                            </div>
                            <div class="form-group p-2">
                                <label for="lastname">Фамилия</label>
                                <input type="text" class="form-control" id="lastname" placeholder="Введите фамилию" name="lname" @if(isset($data['l_name'])) value="{{$data['l_name']}}" @endif>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="card-title">О себе</h5>

                            <div class="form-group p-2">
                                <label for="whosi">Кто я? <strong style="color: red">*</strong></label>
                                <select class="form-select mt-1" id="whosi" name="whosi">
                                    <option selected value="">Выберите один, из предложенных вариантов</option>
                                    <option value="programmer" @if(isset($data['whos_i']) && $data['whos_i'] == 'programmer') selected @endif>Программист</option>
                                    <option value="designer" @if(isset($data['whos_i']) && $data['whos_i'] == 'designer') selected @endif>Дизайнер</option>
                                    <option value="vasyl_kit" @if(isset($data['whos_i']) && $data['whos_i'] == 'vasyl_kit') selected @endif>Вэсыль кiт</option>
                                </select>

                                <div class="form-check mt-1">
                                    <input @if(isset($data['public_whos_i']) && $data['public_whos_i']) checked @endif type="checkbox" class="form-check-input" id="publicWhosi" name="public_whosi">
                                    <label class="form-check-label" for="publicWhosi">Видно всем</label>
                                </div>
                            </div>

                            <div class="form-group p-2">
                                <label for="start_sum_block">Максимальная сумма запуска <strong style="color: red">*</strong></label>
                                <div id="start_sum_block" class="row mt-2">
                                    <div class="col-4 float-start">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="maxStartSum" id="radioFloatStart1" checked value="max_start_1">
                                            <label class="form-check-label" for="radioFloatStart1">
                                                0 - 50 000 ₽
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="maxStartSum" id="radioFloatStart2" value="max_start_2" @if(isset($data['max_start_sum']) && $data['max_start_sum'] == 'max_start_2') checked @endif>
                                            <label class="form-check-label" for="radioFloatStart2">
                                                50 000 - 100 000 ₽
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="maxStartSum" id="radioFloatStart3" value="max_start_3" @if(isset($data['max_start_sum']) && $data['max_start_sum'] == 'max_start_3') checked @endif>
                                            <label class="form-check-label" for="radioFloatStart3">
                                                100 000 - 500 000 ₽
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="maxStartSum" id="radioFloatStart4" value="max_start_4" @if(isset($data['max_start_sum']) && $data['max_start_sum'] == 'max_start_4') checked @endif>
                                            <label class="form-check-label" for="radioFloatStart4">
                                                500 000 - 2 500 000 ₽
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-4 float-end">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="maxStartSum" id="radioFloatStart5" value="max_start_5" @if(isset($data['max_start_sum']) && $data['max_start_sum'] == 'max_start_5') checked @endif>
                                            <label class="form-check-label" for="radioFloatStart5">
                                                2 500 000 - 5 000 000 ₽
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="maxStartSum" id="radioFloatStart6" value="max_start_6" @if(isset($data['max_start_sum']) && $data['max_start_sum'] == 'max_start_6') checked @endif>
                                            <label class="form-check-label" for="radioFloatStart6">
                                                Больше 5 000 000 ₽
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="publicFloatStart" name="public_max_start_sum" @if(isset($data['public_max_start_sum']) && $data['public_max_start_sum']) checked @endif>
                                    <label class="form-check-label" for="publicFloatStart">Видно всем</label>
                                </div>
                            </div>

                            <div class="float-start mt-5">
                                <button class="btn btn-outline-success btn-large">СОХРАНИТЬ ИЗМЕНЕНИЯ</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <br><br><br>
    </div>
@endsection