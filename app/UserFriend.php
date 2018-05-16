<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserFriend
 *
 * @mixin \Eloquent
 * @property int $user_id
 * @property int $friend_id
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFriend whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFriend whereFriendId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFriend whereUserId($value)
 */
class UserFriend extends Model
{
    protected $table = 'user_friends';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * @param int $user_id
     * @param int $friend_id
     *
     * @return bool
     */
    public static function isExists($user_id, $friend_id)
    {
        return self::where('user_id', $user_id)
            ->where('friend_id', $friend_id)
            ->exists();
    }

    /**
     * @param int $user_id
     * @param int $friend_id
     *
     * @return mixed
     */
    public static function getByUserIdAndPersonId($user_id, $friend_id)
    {
        return self::where('user_id', $user_id)
            ->where('friend_id', $friend_id)
            ->get()
            ->first();
    }

    /**
     * @param int $user_id
     *
     * @return array
     */
    public static function getFriendIds($user_id)
    {
        return UserFriend::where('user_id', $user_id)
            ->pluck('friend_id')
            ->toArray();
    }
}
