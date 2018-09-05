<?php

namespace App\Console\Commands;

use App\FriendListChange;
use App\FriendsStatus;
use App\UserFriend;
use Illuminate\Console\Command;

class DeleteUserFriend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userFriend:delete';

    private $user_friend;

    /**
     * Create a new command instance.
     *
     * @param UserFriend $user_friend
     *
     * @return void
     */
    public function __construct(UserFriend $user_friend)
    {
        $this->user_friend = $user_friend;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        UserFriend::where('user_id', $this->user_friend->user_id)
            ->where('friend_id', $this->user_friend->friend_id)
            ->delete();

        $is_exists_for_other_users = UserFriend::where('friend_id', $this->user_friend->friend_id)
            ->get()
            ->isNotEmpty();
        if (!$is_exists_for_other_users) {
            FriendListChange::where('user_id', $this->user_friend->friend_id)
                ->delete();
            FriendsStatus::where('user_id', $this->user_friend->friend_id)
                ->delete();
        }
    }
}
