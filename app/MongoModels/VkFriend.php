<?php

namespace App\MongoModels;

/**
 * Class VkFriend
 * @package App\MongoModels
 */
class VkFriend extends AbstractMongoModel
{
    protected static $collection_name = 'vk_friends';

    /**
     * @param int $owner_id
     * @return array
     */
    public static function getFriends(int $owner_id)
    {
        $vk_friends = self::query()
            ->where('user_id', '=', $owner_id)
            ->first();

        return $vk_friends ? $vk_friends->toArray() : [];
    }
}
