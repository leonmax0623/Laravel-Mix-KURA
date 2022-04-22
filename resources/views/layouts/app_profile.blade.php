@extends('layouts.app')

@section('title-block')@yield('title-block')@endsection

@section('script-header')
    @yield('script-header')
@endsection

@section('header')
    @if(Request::path() != 'auth' && Request::path() != 'non_disclosure')
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">KURA</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link @if(Request::path() == '/') active @endif" aria-current="page" href="/">Главная</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(strpos(Request::path(), 'messages') !== false) active @endif" aria-current="page" href="/messages/0">Мои сообщения</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(strpos(Request::path(), 'friends') !== false) active @endif" aria-current="page" href="/friends">Мои друзья</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(strpos(Request::path(), 'modules') !== false) active @endif" aria-current="page" href="/modules">Обучение</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(strpos(Request::path(), 'profile') !== false) active @endif" aria-current="page" href="/profile">Мой кабинет</a>
                        </li>
                    </ul>



                    <ul class="navbar-nav me-2 mb-lg-0">
                        <li class="nav-item nav-admin-link" style="display: none;">
                            <a class="btn bg-dark text-white rounded-3" aria-current="page" href="/admin">Перейти в админ-панель</a>
                        </li>
                        <li class="nav-item dropstart">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-user"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
                                <li><a class="dropdown-item" href="#">Какая-то ссылка</a></li>
                                <li><a class="dropdown-item" href="#">Какая-то ссылка 2</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item log-out-link" href="#"><i class="fa-solid fa-arrow-right-from-bracket"></i>&nbsp;Выйти</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <script>
            $(function() {
                $.ajax({
                    url: "/check-admin",
                    type: "POST",
                    data: {
                        _token: _token
                    },
                    success: function (response) {
                        if(response['status'] == 'ok')
                            $('.nav-admin-link').show();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            })
        </script>
    @endif
@endsection

@section('content')
    @yield('content')
@endsection

@section('script-footer')
    @yield('script-footer')
@endsection
