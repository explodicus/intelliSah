<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFailSubscriptionsGameSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->longText('fail_subscriptions')->nullable();
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
            $table->dropColumn('fail_subscriptions');
        });
    }
}
