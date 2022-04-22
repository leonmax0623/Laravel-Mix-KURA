@extends('layouts.app_profile')

@section('title-block')Принятия соглашения о не разглошении@endsection

<style>
    body {
        background: linear-gradient(65deg, #EECFBA, #C5DDE8);
    }
</style>

@section('content')
        <div class="container h-100">
            <div class="row justify-content-center align-items-center">
                <div class="col-xl-5 col-lg-6 col-md-7 col-sm-11 col-11 p-5">
                    <form class="text-center form" method="post" action="{{ route('member-non-disclosure') }}">
                        @csrf

                        <div class="card shadow mt-3">
                            <div class="card-header">
                                <h4 class="h2 text-primary text-center">Принятие соглашения о не разглошении</h4>
                                <small class="d-block text-center">
                                    Блааа-блаа-блаа
                                </small>
                            </div>

                            <div class="card-body">
                                <p>Какой-то текст о не разглошении информации</p>
                            </div>
                            <input type="submit" value="Принять" class="btn btn-block btn-lg shadow btn-outline-dark m-3 mt-0">
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection

