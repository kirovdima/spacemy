<?php

namespace App\MongoModels;

class VkUser extends AbstractMongoModel
{
    protected static $collection_name = 'vk_users';

    /**
     * @param int $user_id
     * @return array
     */
    public static function getUser(int $user_id): array
    {
        $user = self::query()
            ->where('id', '=', $user_id)
            ->first()
            ->toArray();

        return $user;
    }
}
