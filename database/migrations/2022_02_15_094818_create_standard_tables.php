<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('password');
            $table->tinyInteger('password_changed')->default(0);
            $table->string('auth_hash')->nullable();
            $table->integer('tariff_id');
            $table->tinyInteger('blocked')->default(0);
            $table->string('dev_key', 30);
            $table->enum('role', ['root', 'admin', 'curator', 'student'])->default('student');
            $table->timestamps();
        });

        Schema::create('members_log', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->dateTime('date');
            $table->string('text');
            $table->string('type');
        });

        Schema::create('members_information', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->string('field_name', 100);
            $table->string('value');
            $table->tinyInteger('public')->default(0);
        });

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('path');
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            $table->integer('admin_id'); //Ссылка на администратора
            $table->integer('commander_id')->nullable(); //Ссылка на пользователя, который стал коммандиром
        });

        Schema::create('member_group', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id');
            $table->integer('member_id');
        });

        Schema::create('tariff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('date_open');
            $table->dateTime('date_close')->nullable();
        });

        Schema::create('module', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
        });

        Schema::create('module_tariff', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id');
            $table->integer('tariff_id');
            $table->dateTime('date_open');
        });

        Schema::create('lesson_tariff', function (Blueprint $table) {
            $table->id();
            $table->integer('lesson_id');
            $table->integer('tariff_id');
        });

        Schema::create('lesson_files', function (Blueprint $table) {
            $table->id();
            $table->integer('lesson_id');
            $table->string('file_url');
            $table->string('file_url_reserve')->nullable();
            $table->enum('file_type', ['lesson_video','pdf','audio','document']);
            $table->integer('sort')->nullable();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id');
            $table->string('name');
            $table->text('description');
            $table->integer('prev_id')->nullable();
            $table->integer('next_id')->nullable();
            $table->text('homework')->nullable();
        });

        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->integer('lesson_id');
            $table->text('question');
            for ($i = 1; $i <= 6; $i++) {
                $table->string('answer_'.$i)->nullable();
            }
            $table->string('answer_timing')->nullable();
            $table->tinyInteger('answer_correct')->nullable();
        });

        Schema::create('tests_answers', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->integer('test_id');
            $table->tinyInteger('answer');
        });

        Schema::create('member_lessons', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->integer('lesson_id');
            $table->tinyInteger('done_test');
        });

        Schema::create('homework_draft', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->integer('lesson_id');
            $table->text('text');
            $table->text('files')->nullable();
            $table->enum('status', ['draft', 'send_to_review']);
            $table->dateTime('add_date');
            $table->dateTime('send_to_review_date')->nullable();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->integer('lesson_id')->nullable();
            $table->integer('parent_id')->nullable();//айди прошлого сообщения
            $table->integer('from_id');//айди member-а который отправил сообщение
            $table->integer('to_id');//айди member-а который получил сообщение
            $table->text('text');
            $table->dateTime('send_date');
            $table->dateTime('opened_date')->nullable();
            $table->tinyInteger('opened')->default(0);
            $table->enum('message_type', ['message', 'file', 'document']);
            $table->tinyInteger('deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('standard_tables');
    }
};
