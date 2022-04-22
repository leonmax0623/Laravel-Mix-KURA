@extends('layouts.app')

@section('title-block')@yield('title-block')@endsection

@section('script-header')
    @yield('script-header')
@endsection

@section('header')
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin">KURA | ADMIN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link @if(Request::path() == 'admin') active @endif" aria-current="page" href="/admin">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Request::path() == 'admin/tariff') active @endif" aria-current="page" href="/admin/tariff">Тарифы</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Request::path() == 'admin/module') active @endif" aria-current="page" href="/admin/module">Модули</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Request::path() == 'admin/lessons') active @endif" aria-current="page" href="/admin/lessons">Уроки</a>
                    </li>
                </ul>



                <ul class="navbar-nav me-2 mb-lg-0">
                    <li class="nav-item nav-admin-link">
                        <a class="btn bg-secondary rounded-3" aria-current="page" href="/">Перейти в обычный профиль</a>
                    </li>
                    <li class="nav-item dropstart">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-astronaut"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
                            <li><a class="dropdown-item log-out-link" href="#"><i class="fa-solid fa-arrow-right-from-bracket"></i>&nbsp;Выйти</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
@endsection

@section('content')
    @yield('content')
@endsection

@section('script-footer')
    @yield('script-footer')
@endsection
