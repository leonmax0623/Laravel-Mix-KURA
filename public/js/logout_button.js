$('.log-out-link').on('click', function () {
    $.ajax({
        url: "/log-out",
        type: "POST",
        data: {
            _token: _token
        },
        success: function (response) {
            if (response['status'] == 'ok')
                window.location = '/auth';
            else
                alert('Произошла ошибка... Попробуйте перезапустить страницу');

            console.log(response);
        },
        error: function (error) {
            alert('Произошла ошибка... Попробуйте перезапустить страницу');
            console.log(error);
        }
    });
})