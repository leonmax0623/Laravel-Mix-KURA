@extends('layouts.app_profile')

@section('title-block')
    Друзья
@endsection

@section('content')
    <div class="container">

            <h1 class="mt-5">Друзья</h1>
            <form action="{{ route('member-friends-send') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-6">
                        <button class="btn btn-outline-secondary col-12" @if($type == 'all_members') disabled @endif name="all_members" type="submit">Все участники</button>
                    </div>

                    <div class="col-6">
                        <button class="btn btn-outline-secondary col-12" @if($type == 'friends') disabled @endif name="friends" type="submit">Мои друзья ({{$friends_count}})</button>
                    </div>

                    @if($type == 'all_members')
                        <div class="col-12 mt-3">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Имя пользователя" aria-label="Recipient's username" aria-describedby="button-addon2" name="friend_search" @if(isset($name_input)) value="{{$name_input}}" @endif>
                                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Поиск <i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                        </div>
                    @endif
                </div>
            </form>

            @foreach($friends as $friend)
                <div class="row border p-3 mt-3">
                    <div class="col-md-1 bg-white">
                        <img src="@if(!$friend['profile_img']) /public/system/no-image.png @else {{$friend['profile_img']}} @endif" class="rounded-circle" style="width: 80px; height: 80px">
                    </div>

                    <div class="col-md-11 bg-white">
                        <div class="text-box float-start col-md-6">
                            <h3>{{$friend['name']}} {{$friend['l_name']}}</h3>
                            <small class="text-gray">{{$friend['whos_i']}}</small><br>
                        </div>



                        <div class="float-end row col-md-6">
                            <div class="col-md-6 button-block col-sm-12">
                                <button id="{{$friend['id']}}" class="btn @if(!$friend['is_friend']) btn-danger @else btn-outline-secondary @endif p-3 text-center btnDeleteAddFriend" style="margin-top: 5%">@if(!$friend['is_friend']) Добавить в друзья @else Удалить из друзей @endif</button>
                            </div>

                            <div class="col-md-6 button-block col-sm-12">
                                <a href="{{ route('other-profile', $friend['dev_key']) }}" class="btn btn-outline-secondary p-3 text-center" style="margin-top: 5%">Перейти в профиль</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="d-flex justify-content-center">
                {!! $friends->links() !!}
            </div>
            <br><br><br>
    </div>

    <script>
        $('.btnDeleteAddFriend').on('click', function(object){
            let friend_id = $(this).attr('id');
            let type = 'add_friend';
            if($(this).text() == ' Удалить из друзей ')
                type = 'delete_friend';

            $.ajax({
                url: "/friends-add-delete",
                type: "POST",
                data: {
                    friend_id: friend_id,
                    type: type,
                    _token: _token
                },
                success: function(response){
                    if(response['status'] == 'error'){
                        alert(response['message']);
                    }else{
                        window.location = '/friends';
                    }
                },
                error: function (error) {
                    alert('Произошла ошибка... Попробуйте перезапустить страницу')
                    console.log(error);
                }
            });
        })
    </script>
@endsection