<?php

namespace App\MongoModels;

class VkFriend extends AbstractMongoModel
{
    protected static $collection_name = 'vk_friends';

    public static function getFriends($owner_id)
    {
        $vk_friends = self::query()
            ->where('user_id', '=', $owner_id)
            ->first()
            ->toArray();

        return $vk_friends;
    }
}
