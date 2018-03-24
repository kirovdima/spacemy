<?php

namespace App\Console\Commands;

use App\FriendsList;
use App\Services\Vk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckFriendsList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'friendsList:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Friends List check';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $vkClient = new Vk();

        $user_friends = DB::select('
            SELECT u.access_token, u.user_id, uf.friend_id
            FROM users u 
              JOIN user_friends uf ON u.user_id = uf.user_id
        ');

        foreach ($user_friends as $user_friend) {
            $vkFriends = $vkClient->getFriends($user_friend, $user_friend->friend_id);

            $friend_list = DB::table('friends_list')
                ->select('friends')
                ->where('user_id', $user_friend->friend_id)
                ->orderBy('updated_at', 'DESC')
                ->limit(1)
                ->get();

            if ($friend_list->isEmpty()) {
                $friends = $vkFriends['items'];

                $friend_list = new FriendsList();
                $friend_list->user_id = $user_friend->friend_id;
                $friend_list->friends = serialize($friends);
                $friend_list->save();
            } else {
                $friends = unserialize($friend_list[0]->friends);
            }

            $friendIds = array_map(function ($friend) { return $friend['id']; }, $friends);
            $vkFriendIds = array_map(function ($friend) { return $friend['id']; }, $vkFriends['items']);

            if (
                $deleted_ids = array_diff($friendIds, $vkFriendIds)
                || $added_ids = array_diff($vkFriendIds, $friendIds)
            ) {
                $friend_list = new FriendsList();
                $friend_list->user_id = $user_friend->friend_id;
                $friend_list->friends = serialize($vkFriends['items']);
                $friend_list->save();
            }

            sleep(1);
        }
    }
}
