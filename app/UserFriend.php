<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;

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
     * @return $this
     */
    public static function getByUserIdAndPersonId(int $user_id, int $friend_id)
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

    /**
     * @param int $user_id
     * @return int
     */
    public static function getPersonsCount(int $user_id)
    {
        return self::where('user_id', $user_id)
            ->count();
    }

    /**
     * @return mixed|string
     */
    public function getFormattedCreatedAt()
    {
        Date::setLocale('ru');
        $formatted_created_at = $this->created_at > date('Y-m-d H:i:s', strtotime('-1 days'))
            ? (new Date($this->created_at))->ago()
            : (new Date($this->created_at))->format('j F Y')
        ;

        return $formatted_created_at;
    }
}
