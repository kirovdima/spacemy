<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FriendsList
 *
 * @property int $user_id
 * @property string $friends
 * @property \Carbon\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsList whereFriends($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsList whereUserId($value)
 * @mixin \Eloquent
 */
class FriendsList extends Model
{
    protected $table = 'friends_list';

    public $timestamps = false;

    public static function getByUserId($user_id)
    {
        $friend_list = self::where('user_id', $user_id)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->toArray();

        return $friend_list;
    }
}
