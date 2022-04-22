<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Авторизация пользователя - MEMBER
Route::get('/auth', function () { return view('profile/auth', ['type' => 'email', 'step' => 1, 'email' => '', 'phone' => '']); });


Route::post('/auth', 'MembersController@memberAuth')->name('member-auth');
Route::post('/log-out', 'MembersController@memberLogOut')->name('member-log-out');
Route::post('/check-admin', 'MembersController@checkAdmin')->name('check-admin');

//Окно о не разглошении информации
Route::get('/non_disclosure', function () { return view('profile/non_disclosure'); });
Route::post('/non_disclosure', 'MembersController@memberNonDisclosure')->name('member-non-disclosure');

//Личный кабинет
Route::group(['middleware' => ['profile_auth', 'check_non_disclosure']], function(){
    Route::get('/profile', 'MembersController@memberProfile');
    Route::post('/profile-send', 'MembersController@memberProfileSend')->name('profile-send');
});

//Маршрутизация пользователя - MEMBER
Route::group(['middleware' => ['profile_auth', 'check_non_disclosure', 'check_profile_information']], function(){
    Route::get('/', function () { return view('profile/index'); }); // Главная страница

    //Сообщения
    Route::get('/messages/{dev_key}', 'MessagesController@getOneMessage')->name('get-one-message'); // Страница с сообщениями - но с определенным
    Route::post('/messages-send', 'MessagesController@sendMessage')->name('send-message');//Отправка сообщения
    Route::post('/messages-search', 'MessagesController@searchMember')->name('search-member-message');//Поиск пользователя

    // ОБУЧЕНИЕ
    // Список модулей
    Route::get('/modules', 'ModuleController@getAllMemberModules');
    Route::get('/modules/{id}', 'ModuleController@getOneModule')->name('profile-lessons');
    // Урок
    Route::get('/lessons/{id}', 'ModuleController@getOneProfileLesson')->name('profile-lesson');

    Route::get('/friends', 'MembersController@getMembersFriends');
    Route::post('/friends', 'MembersController@getMembersFriendsSend')->name('member-friends-send');
    Route::post('/friends-add-delete', 'MembersController@friendsAddDelete')->name('friends-add-delete');

    Route::get('/profile/{id}', 'MembersController@otherMemberProfile')->name('other-profile');
});


//Маршрутизация администратора - ADMIN
Route::group(['middleware' => ['admin_auth']], function(){
    Route::get('/admin', function () { return view('admin/index'); }); // Главная страница

    //Тарифы
    Route::get('/admin/tariff', 'TariffController@getAllTariff');

    Route::post('/admin/tariff-delete', 'TariffController@deleteTariff')->name('tariff-delete');

    Route::get('/admin/tariff-add-update/{id}', 'TariffController@addUpdateTariff')->name('tariff-add-update');
    Route::post('/admin/tariff-add-update/{id}', 'TariffController@addUpdateTariffSubmit')->name('tariff-add-update-submit');

    //Модули
    Route::get('/admin/module', 'ModuleController@getAllModule');

    Route::post('/admin/module-delete', 'ModuleController@deleteModule')->name('module-delete');

    Route::get('/admin/module-add-update/{id}', 'ModuleController@addUpdateModule')->name('module-add-update');
    Route::post('/admin/module-add-update/{id}', 'ModuleController@addUpdateModuleSubmit')->name('module-add-update-submit');
    Route::post('/admin/module-add-delete-tariff', 'ModuleController@addDeleteTariffModule');

    //Уроки
    Route::get('/admin/lessons', 'LessonsController@getAllLessons');
    Route::get('/admin/lessons/{id}', 'LessonsController@getOneLesson')->name('admin-lesson');
    Route::get('/admin/lesson-add-update/{id}', 'LessonsController@addUpdateLesson')->name('lesson-add-update');
    Route::post('/admin/lesson-add-update/{id}', 'LessonsController@addUpdateLessonSubmit')->name('lesson-add-update-submit');
});