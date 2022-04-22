@extends('layouts.app_profile')

@section('title-block')Авторизация@endsection

<style>
    body {
        background: linear-gradient(65deg, #EECFBA, #C5DDE8);
    }
</style>

@section('script-header')
    <script src="{{ asset('/js/mask/jquery.inputmask.bundle.min.js') }}"></script>
    <script src="{{ asset('/js/mask/jquery.inputmask-multi.min.js') }}"></script>
    <script src="{{ asset('/js/mask/site.js') }}"></script>
@endsection

@section('content')
        <div class="container h-100">
            <div class="row justify-content-center align-items-center">
                <div class="col-xl-5 col-lg-6 col-md-7 col-sm-11 col-11 p-5">
                    <form class="text-center form" method="post" action="{{ route('member-auth') }}">
                        @csrf

                        <div class="card shadow mt-3">
                            <div class="card-header p-4">
                                @if($step == 1 || $step == 2)
                                    <h4 class="h2 text-primary text-center">Вход в личный кабинет</h4>
                                    <small class="d-block text-center">
                                        Для продолжения, нужно ввести email или телефон
                                    </small>
                                @elseif($step == 3)
                                    <h4 class="h2 text-primary text-center">Добро пожаловать</h4>
                                    <span>Вам нужно задать пароль</span>
                                    <small class="d-block text-center">
                                        Ранее Вы не указывали пароль. Сейчас самое время сделать это. Придумайте пароль, и заполните форму ниже:
                                    </small>
                                @endif
                            </div>

                            <div class="card-body p-4">
                                @if(isset($error_messages))
                                    @foreach($error_messages as $error)
                                        <div class="alert alert-warning">
                                            {{ $error }}
                                        </div>
                                    @endforeach
                                @endif

                                @if($step == 1)

                                    <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button type="button" name="type" value="email" class="changeTypeEmail nav-link @if($type == 'email') active @endif">Email</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button type="button" name="type" value="phone" class="changeTypePhone nav-link @if($type == 'phone') active @endif">Телефон</button>
                                        </li>
                                    </ul>


                                    <div class="form-group form-enter-email" @if($type == 'phone') style="display: none" @endif>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Укажите Ваш email" value="{{ $email }}">
                                        <small class="form-text text-muted">&nbsp;</small>
                                    </div>

                                    <div class="form-group form-enter-phone" @if($type == 'email') style="display: none" @endif>
                                        <input type="tel" name="phone" class="form-control" id="customer_phone" placeholder="Укажите Ваш телефон" value="{{ $phone }}">
                                        <small class="form-text text-muted mt-1">Номер телефона в международном формате</small>
                                    </div>

                                @elseif($step == 2)
                                    <div class="form-group">
                                        <label for="password">Укажите Ваш пароль:</label>
                                        <input type="password" name="password" class="form-control" id="password" value="">
                                    </div>
                                @elseif($step == 3)
                                    <div class="form-group">
                                        <label for="password_1" class="text-primary font-weight-bold">Укажите Ваш пароль</label>
                                        <input type="password" name="first_password" class="form-control border-primary" id="password_1" aria-describedby="password_1_help">
                                        <small id="password_1_help" class="form-text text-muted">Пароль должен быть не короче 6 символов</small>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="password_2" class="font-weight-bold text-primary">Укажите подтверждение пароля</label>
                                        <input type="password" name="second_password" class="form-control border-primary" id="password_2" aria-describedby="password_2_help">
                                        <small id="password_2_help" class="form-text text-muted">Пароль и подтверждение пароля должны совпадать</small>
                                    </div>
                                @endif
                            </div>
                            <input type="submit" value="Продолжить" class="btn btn-block btn-lg shadow btn-outline-dark m-3 mt-0 btnSendAuth">
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection

@section('script-footer')
    <script>
        $(".changeTypePhone").click(function() {
            $(".changeTypeEmail").removeClass('active');
            $(this).addClass('active');
            $('.form-enter-email').hide();
            $('.form-enter-phone').show();
            $('#email').val('');
        });

        $(".changeTypeEmail").click(function() {
            $(".changeTypePhone").removeClass('active');
            $(this).addClass('active');
            $('.form-enter-phone').hide();
            $('.form-enter-email').show();
            $('#customer_phone').val('');
        });


    </script>
@endsection