<?php

namespace App\Jobs;

use App\FriendsStatus;
use App\Services\Vk;
use App\UserFriend;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Запрашиваем ВКонтакте последнюю дату визита людей, за которыми следит $user.
 *
 * Class CheckUserFriendsStatusJob
 * @package App\Jobs
 */
class CheckUserFriendsStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const QUEUE_NAME = 'check_user_friends_status';

    const TIME_INTERVAL = 600;
    const SLEEP_INTERVAL = 1000000;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->queue = config('queue.prefix') . self::QUEUE_NAME;

        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $vkClient = new Vk();

        $friends = UserFriend::where('user_id', $this->user->user_id)
            ->get()
            ->toArray();

        if (!$friends) {
            return ;
        }

        $friend_ids = array_map(function ($friend) { return $friend['friend_id']; }, $friends);

        $vkFriends = $vkClient->getUsers($this->user, $friend_ids);
        if (!$vkFriends) {
            return ;
        }

        foreach ($vkFriends as $vkFriend) {
            if (isset($vkFriend['deactivated'])) {
                //@todo сохранять в user_friends и больше не опрашивать его статус
                continue;
            }

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

        // @todo
        usleep(self::SLEEP_INTERVAL);
    }
}
