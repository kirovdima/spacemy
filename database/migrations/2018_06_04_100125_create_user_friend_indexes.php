<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFriendIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_friends', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('friend_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_friends', function (Blueprint $table) {
            $table->dropIndex('user_id');
            $table->dropIndex('friend_id');
            $table->dropIndex('created_at');
        });
    }
}
