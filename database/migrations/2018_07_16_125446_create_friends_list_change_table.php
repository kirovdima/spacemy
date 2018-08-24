<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\FriendListChange;

class CreateFriendsListChangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friends_list_change', function (Blueprint $table) {
            $table->integer('user_id');
            $table->enum('status', [FriendListChange::STATUS_ADD, FriendListChange::STATUS_DELETE]);
            $table->integer('friend_id');
            $table->timestamp('created_at');

            $table->index('user_id');
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
        Schema::dropIfExists('friends_list_change');
    }
}
