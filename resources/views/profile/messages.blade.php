@extends('layouts.app_profile')

@section('script-header')
    <link rel="stylesheet" href="{{ asset('/css/messanger.css') }}">
    <style>
        .image-upload>input {
            display: none;
        }
    </style>
@endsection

@section('title-block')
    Мои сообщения
@endsection

@section('content')
    <div class="container">
        <h1 class="mt-5 text-center">Мои сообщения</h1>

        <div class="row rounded-lg overflow-hidden shadow">
            <!-- Users box-->
            <div class="col-md-5 col-sm-12 px-0">
                <div class="bg-white">

                    <div class="bg-gray px-4 py-2 bg-light">
                        <p class="h5 mb-0 py-1">Недавние</p>
                        <form action="{{ route('search-member-message', $current_member_dev) }}" method="POST">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="member_search" name="member_search" placeholder="Почта пользователя...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btnSearchMember" type="submit">Поиск <i class="fa-solid fa-magnifying-glass"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="messages-box">
                        <div class="list-group rounded-0 correspondences-box">
                            @foreach($members_correspondence as $member)
                                <a href="{{ route('get-one-message', $member['dev_key']) }}" class="list-group-item list-group-item-action text-white rounded-0 @if($current_member_dev == $member['dev_key']) active @endif">
                                    <div class="media">
                                        @if(isset($member['profile_img']) && $member['profile_img'])
                                            <img src="{{$member['profile_img']}}" alt="user" width="50" class="rounded-circle">
                                        @else
                                            <img src="/public/system/no-image.png" alt="user" width="50" class="rounded-circle">
                                        @endif
                                        <div class="media-body ml-4 mt-2 @if($current_member_dev != $member['dev_key']) text-dark @else text-white @endif">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <h6 class="mb-0" >{{$member['name']}}</h6>
                                            </div>
                                            <p class="font-italic mb-0 text-small">{{$member['email']}}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- Chat Box-->
            <div class="col-md-7 col-sm-12 px-0">
                <div class="px-4 py-5 chat-box bg-white">
                    @foreach($chat as $ch)
                        <div class="media w-50 mb-3">
                            @if(isset($ch['member_profile_img']) && $ch['member_profile_img'])
                                <img src="{{$ch['member_profile_img']}}" width="50" class="rounded-circle">
                            @else
                                <img src="/public/system/no-image.png" alt="user" width="50" class="rounded-circle">
                            @endif
                            <span class="small text-muted">@if($ch['from_id'] == $member_id) Вы: @else {{$ch['member_name']}}: @endif</span>
                            <div class="media-body ml-3 mt-2">
                                <div class="@if($ch['from_id'] != $member_id) bg-light @else bg-primary @endif rounded py-2 px-3 mb-2">
                                    @if($ch['parent_id'])
                                        <br>
                                        <figcaption class="blockquote-footer @if($ch['from_id'] == $member_id) text-dark @endif">
                                            В ответ на: <cite title="Source Title">{{$ch['parent_text']}}</cite>
                                        </figcaption>
                                    @endif

                                    @if($ch['message_type'] == 'message')
                                        <p class="text-small mb-0 @if($ch['from_id'] != $member_id) text-muted @else text-white @endif">{{$ch['text']}}</p>
                                    @elseif($ch['message_type'] == 'file')
                                        <a href="{{$ch['text']}}" target="_blank"><i class="fa-solid fa-file-arrow-down p-1 @if($ch['from_id'] == $member_id) text-white @else text-dark @endif" style="font-size:34px"></i></a>
                                    @endif
                                    <br>
                                    <p class="float-start text-small mb-0 @if($ch['from_id'] != $member_id) text-muted @else text-white @endif">{{$ch['send_date']}}</p>
                                    @if($ch['message_type'] != 'file')
                                        <button class="float-end text-dark btnReplyToMessage btn btn-sm" id="{{$ch['id']}}"><i class="fa-solid fa-reply"></i></button>
                                    @endif
                                    <br>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>

                <!-- Typing area -->
                <form action="#" class="bg-light">
                    <div class="list-block p-1">
                        <div class="files-list-block">
                        </div>
                    </div>
                    <div class="input-group">
                        <textarea style="resize: none;" type="text" placeholder="Сообщение..." aria-describedby="button-addon2" class="form-control rounded-0 border-0 py-4 bg-light textToSend"></textarea>
                        <div class="input-group-append">
                            <button title="Отправить" id="{{$current_member_dev}}" type="submit" class="btn btn-link btnSendMessage"><i class="fa fa-paper-plane"></i></button>
                            <br>
                            <div class="image-upload text-center" title="Загрузить файл">
                                <label for="file-input">
                                    <i class="fa-solid fa-file-arrow-up"></i>
                                </label>

                                <input id="file-input" type="file" multiple="multiple" />
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <br><br><br>
    </div>

    <script>
        let files; // переменная содержит данные файлов
        let message_reply = 0;

        // заполняем переменную данными файлов, при изменении значения file поля
        $('input[type=file]').on('change', function(){
            files = this.files;

            $('.files-list-block').empty();
            $.map(files, function(val) {
                $('.files-list-block').append('<span class="badge rounded-pill bg-secondary text-center">'+val.name+'</span>&nbsp;')
            })
        });

        $(".chat-box").scrollTop($(".chat-box").prop('scrollHeight'));

        $('.btnReplyToMessage').on('click', function(){
            message_reply = $(this).attr('id');

            $('.reply-message-badge').remove();
            $('.list-block').prepend('<span class="text-white badge bg-secondary text-dark reply-message-badge">В ответ на сообщение <button type="button" class="text-white btn btn-sm btnCancelReply"><i class="fa-solid fa-xmark"></i></button></span>');
        })


        $(document).on ("click", ".btnCancelReply", function () {
            message_reply = 0;
            $('.reply-message-badge').remove();
        });

        $('.btnSearchMember').on('click', function(e){
            e.preventDefault();
            let member_email = $('#member_search').val();

            $.ajax({
                url: "/messages-search",
                type: "POST",
                data: {
                    _token: _token,
                    member_email: member_email
                },
                success: function (response) {
                    if(response['status'] == 'error'){
                        alert(response['message']);
                    }else{
                        $('.correspondences-box').empty();
                        jQuery.each( response['data'], function( i, val ) {
                            console.log(val);
                            $('.correspondences-box').append('<a href="/messages/'+val["dev_key"]+'" class="list-group-item list-group-item-action text-white rounded-0"> <div class="media"> <img src="'+val["profile_img"]+'" alt="user" width="50" class="rounded-circle"> <div class="media-body ml-4 mt-2" style="color: black"> <div class="d-flex align-items-center justify-content-between mb-1"> <h6 class="mb-0" >'+val["name"]+'</h6> </div> <p class="font-italic mb-0 text-small">'+val["email"]+'</p> </div> </div> </a>');
                        });
                    }
                    console.log(response);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        })

        $('.btnSendMessage').on('click', function (e){
            e.stopPropagation();
            e.preventDefault();

            let devkeyToSend = $(this).attr('id');
            let text = $('.textToSend').val();

            if(!text.trim())
                text = text.trim();

            let data = new FormData();
            $.each( files, function( key, value ){
                data.append( key, value );
            });

            data.append('_token', _token);
            data.append('text', text);
            data.append('member_dev_key', devkeyToSend);
            data.append('parent_id', message_reply)

            $.ajax({
                url: "/messages-send",
                type: "POST",
                data: data,
                processData: false,
                contentType: false,
                success: function (response) {
                    if(response['status'] == 'ok'){
                        if(response['data']['text']) {
                            let in_reply = '';
                            if(response['data']['parent_text']){
                                in_reply = '<br> <figcaption class="blockquote-footer text-dark">В ответ на: <cite title="Source Title">'+response["data"]["parent_text"]+'</cite> </figcaption>';
                            }
                            $('<div class="media w-50 mb-3"> <img src="' + response['data']['member_img_path'] + '" alt="user" width="50" class="rounded-circle"> <span class="small text-muted">Вы:</span> <div class="media-body ml-3 mt-2"> <div class="bg-primary rounded py-2 px-3 mb-2">'+in_reply+' <p class="text-small mb-0 text-white">' + response["data"]["text"] + '</p> <br> <p class="float-start text-small mb-0 text-white">' + response["data"]["send_date"] + '</p> <button class="float-end text-dark btnReplyToMessage btn btn-sm" id="'+response["data"]["text_message_id"]+'"><i class="fa-solid fa-reply"></i></button><br> </div> </div> </div>').hide().appendTo($('.chat-box')).fadeIn('normal');
                        }
                        if(response['data']['path_array']){
                            $.each( response['data']['path_array'], function( key, value ){
                                $('<div class="media w-50 mb-3"> <img src="' + response['data']['member_img_path'] + '" alt="user" width="50" class="rounded-circle"> <span class="small text-muted">Вы:</span> <div class="media-body ml-3 mt-2"> <div class="bg-primary rounded py-2 px-3 mb-2"> <a href="'+value+'" target="_blank"><i class="fa-solid fa-file-arrow-down p-1" style="color: white; font-size:34px"></i></a> <br> <p class="flot-end text-small mb-0 text-white">' + response["data"]["send_date"] + '</p> </div> </div> </div>').hide().appendTo($('.chat-box')).fadeIn('normal');
                            });
                        }

                        $(".chat-box").scrollTop($(".chat-box").prop('scrollHeight'));
                        $('.textToSend').val('');
                        $('.files-list-block').empty();
                        message_reply = 0;
                        $('.reply-message-badge').remove();
                    }else{
                        alert(response['message']);
                        console.log(response['message']);
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });
    </script>
@endsection