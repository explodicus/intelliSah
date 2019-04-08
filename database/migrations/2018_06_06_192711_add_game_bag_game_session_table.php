<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGameBagGameSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->longText('game_bag')->nullable();
            $table->unsignedInteger('current_subscription_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropColumn('game_bag');
            $table->dropColumn('current_subscription_id');
        });
    }
}
