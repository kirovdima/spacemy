<?php

namespace App\Jobs;

use App\FriendListChange;
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
    public function __construct(int $user_id, int $friend_id = null)
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

        // получаем из mongodb старый список друзей
        $oldVkFriends = VkFriend::getFriends($this->friend_id ?: $this->user_id);
        //~

        $vkClient = new Vk();
        $vkFriends = $vkClient->getFriends($user, $this->friend_id ?: null);

        // сохраняем друзей в mongodb
        VkFriend::where('user_id', $this->friend_id ?: $this->user_id)
            ->update(['friends' => $vkFriends['items'], 'updated_at' => date('Y-m-d H:i:s')], ['upsert' => true]);
        foreach ($vkFriends['items'] as $vkFriend) {
            VkUser::batchUpdate(['id' => $vkFriend['id']], $vkFriend, ['upsert' => true]);
        }
        VkUser::execute();
        //~

        if ($this->friend_id && !empty($oldVkFriends['friends'])) {
            $friendIds   = array_map(function ($friend) { return $friend['id']; }, $oldVkFriends['friends']);
            $vkFriendIds = array_map(function ($friend) { return $friend['id']; }, $vkFriends['items']);

            $added_ids   = array_diff($vkFriendIds, $friendIds);
            $deleted_ids = array_diff($friendIds, $vkFriendIds);

            $common_ids = array_merge($added_ids, $deleted_ids);
            $changes = [];
            foreach ($common_ids as $id) {
                $changes[] = [
                    'user_id'   => $this->friend_id,
                    'friend_id' => $id,
                    'status'    => in_array($id, $added_ids)
                        ? FriendListChange::STATUS_ADD
                        : FriendListChange::STATUS_DELETE,
                ];
            }
            if ($changes) {
                FriendListChange::insert($changes);
            }
        }

        // @todo
        usleep(self::SLEEP_INTERVAL);
    }
}
