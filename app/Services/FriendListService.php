<?php

namespace App\Services;

use App\MongoModels\VkFriend;
use App\UserFriend;
use Jenssegers\Date\Date;

class FriendListService
{
    protected $owner_id = null;

    protected $friends    = null;
    protected $updated_at = null;

    protected $friend_ids = null;

    public function __construct()
    {
    }

    public function setOwnerId(int $owner_id)
    {
        $this->owner_id = $owner_id;
        $this->init();

        return $this;
    }

    protected function init()
    {
        $vk_friends = VkFriend::getFriends($this->owner_id);
        $this->friends    = $vk_friends['friends'];
        $this->updated_at = $vk_friends['updated_at'];

        $this->friend_ids = UserFriend::getFriendIds($this->owner_id);
    }

    public function getFriendIds(): array
    {
        return $this->friend_ids;
    }

    public function getFormattedUpdatedAt(): string
    {
        Date::setLocale('ru');
        return (new Date($this->updated_at))->ago();
    }

    public function getFriends(): array
    {
        return $this->friends;
    }

    public function sortByLastName()
    {
        $sort_by_last_name_function = function ($friend1, $friend2) {
            if ($friend1['last_name'] < $friend2['last_name']) {
                return -1;
            } elseif ($friend1['last_name'] > $friend2['last_name']) {
                return 1;
            } else {
                return 0;
            }
        };
        usort($this->friends, $sort_by_last_name_function);

        return $this;
    }

    public function separateByHasStatAndFirstLetter()
    {
        $friends = [];
        $friends['has_stat'] = [];
        foreach ($this->friends as $vk_friend) {
            if (in_array($vk_friend['id'], $this->friend_ids)) {
                $friends['has_stat'][] = $vk_friend;
            } else {
                $first_letter = mb_substr($vk_friend['last_name'], 0, 1);
                $friends[$first_letter][] = $vk_friend;
            }
        }

        $this->friends = $friends;

        return $this;
    }
}
