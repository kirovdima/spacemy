<?php

namespace App\Jobs;

use App\FriendsList;
use App\MongoModels\VkFriend;
use App\MongoModels\VkUser;
use App\Services\Vk;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Запрашиваем ВКонтакте список друзей пользователя $friend_id и проверяем на предмет изменений.
 *
 * Class CheckUserFriendsListJob
 * @package App\Jobs
 */
class CheckUserFriendsListJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const QUEUE_NAME = 'check_user_friends_list';

    const SLEEP_INTERVAL = 1000000;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var int
     */
    protected $friend_id;

    /**
     * Create a new job instance.
     *
     * @param int $user_id
     * @param int $friend_id
     */
    public function __construct(int $user_id, int $friend_id)
    {
        $this->queue = config('queue.prefix') . self::QUEUE_NAME;

        $this->user_id   = $user_id;
        $this->friend_id = $friend_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->user_id);

        $vkClient = new Vk();
        $vkFriends = $vkClient->getFriends($user, $this->friend_id);

        // сохраняем друзей в mongodb
        VkFriend::where('user_id', $this->friend_id)
            ->update(['friends' => $vkFriends['items']], ['upsert' => true]);

        foreach ($vkFriends['items'] as $vkFriend) {
            VkUser::batchUpdate(['id' => $vkFriend['id']], $vkFriend, ['upsert' => true]);
        }
        VkUser::execute();
        //~

        $friend_list = FriendsList::select(['friends'])
            ->where('user_id', $this->friend_id)
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get();

        if ($friend_list->isEmpty()) {
            $friends = $vkFriends['items'];

            $friend_list = new FriendsList();
            $friend_list->user_id = $this->friend_id;
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
            $friend_list->user_id = $this->friend_id;
            $friend_list->friends = serialize($vkFriends['items']);
            $friend_list->save();
        }

        // @todo
        usleep(self::SLEEP_INTERVAL);
    }
}
