<?php

namespace App\Console\Commands;

use App\FriendListChange;
use App\FriendsList;
use App\MongoModels\VkUser;
use App\Services\Vk;
use App\User;
use App\UserFriend;
use Illuminate\Console\Command;

class FriendsListToFriendsListChangeMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'friends_list:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Мигрируем данные из старой таблицы friends_list в новую friends_list_change';

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
        $friends_lists = FriendsList::query()
            ->orderBy('user_id')
            ->get()
            ->toArray();

        $changes = [];

        $current_user_id = null;
        $prev_friends    = [];
        foreach ($friends_lists as $list) {
            $user_id = $list['user_id'];
            if ($current_user_id != $user_id) {
                $current_user_id = $user_id;
                $prev_friends = unserialize($list['friends']);
            } else {
                $friends = unserialize($list['friends']);
                $friends_diff_function = function ($item1, $item2) {
                    if ($item1['id'] > $item2['id']) {
                        return 1;
                    } elseif ($item1['id'] < $item2['id']) {
                        return -1;
                    } else {
                        return 0;
                    }
                };

                $search_users = [];

                if ($add_friends = array_udiff($friends, $prev_friends, $friends_diff_function)) {
                    foreach ($add_friends as $add_friend) {
                        $changes[] = [
                            'user_id'    => $current_user_id,
                            'status'     => FriendListChange::STATUS_ADD,
                            'friend_id'  => $add_friend['id'],
                            'created_at' => $list['created_at'],
                        ];

                        $vk_user = VkUser::query()
                            ->where('id', '=', $add_friend['id'])
                            ->get();
                        if ($vk_user->isEmpty()) {
                            $search_users[] = $add_friend['id'];
                        }
                    }
                } elseif ($delete_friends = array_udiff($prev_friends, $friends, $friends_diff_function)) {
                    foreach ($delete_friends as $delete_friend) {
                        $changes[] = [
                            'user_id'    => $current_user_id,
                            'status'     => FriendListChange::STATUS_DELETE,
                            'friend_id'  => $delete_friend['id'],
                            'created_at' => $list['created_at'],
                        ];

                        $vk_user = VkUser::query()
                            ->where('id', '=', $delete_friend['id'])
                            ->get();
                        if ($vk_user->isEmpty()) {
                            $search_users[] = $delete_friend['id'];
                        }
                    }
                }

                if ($search_users) {
                    $user_id = UserFriend::query()
                        ->where('friend_id', $current_user_id)
                        ->first()
                        ->value('user_id');
                    $user = User::find($user_id);

                    $vk = new Vk();
                    $vk_users = $vk->getUsers($user, $search_users);
                    sleep(1);
                    foreach ($vk_users as $vk_user) {
                        VkUser::batchUpdate(['id' => $vk_user['id']], $vk_user, ['upsert' => true]);
                }
                    VkUser::execute();
                }

                $prev_friends = $friends;
            }
        }

        FriendListChange::insert($changes);
    }
}
