<?php

namespace App\Console\Commands;

use App\FriendsStatus;
use App\Services\Vk;
use App\User;
use App\UserFriend;
use Illuminate\Console\Command;

class CheckFriendsStatus extends Command
{
    const TIME_INTERVAL = 900;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'friendsStatus:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Friends status check';

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
        dispatch(new \App\Jobs\CheckFriendsStatus(1))->onQueue('check_friends_status');
        return;

        $vkClient = new Vk();

        $users = User::all();
        foreach ($users as $user) {
            $friends = UserFriend::where('user_id', $user->user_id)
                ->get()
                ->toArray();
            $friend_ids = array_map(function ($friend) { return $friend['friend_id']; }, $friends);

            $vkFriends = $vkClient->getUsers($user, $friend_ids);
            if (!$vkFriends) {
                continue;
            }

            foreach ($vkFriends as $vkFriend) {
                $status = $vkFriend['online'] == FriendsStatus::STATUS_ONLINE
                    ? $vkFriend['online']
                    : (time() - $vkFriend['last_seen']['time'] < self::TIME_INTERVAL
                        ? FriendsStatus::STATUS_ONLINE
                        : FriendsStatus::STATUS_OFFLINE
                    );

                $friendStatus = new FriendsStatus();
                $friendStatus->user_id = $vkFriend['id'];
                $friendStatus->status  = $status;
                $friendStatus->save();
            }

            sleep(1);
        }
    }
}
