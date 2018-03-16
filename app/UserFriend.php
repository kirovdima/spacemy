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

    public $timestamps = false;
}
