<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameSessionSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_session_subscribers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('session_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('side');
            $table->timestamps();
            $table->foreign('session_id')->references('id')->on('game_sessions');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_session_subscribers');
    }
}
