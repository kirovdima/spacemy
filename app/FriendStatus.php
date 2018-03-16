<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FriendStatus
 *
 * @mixin \Eloquent
 * @property int $user_id
 * @property int $status
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendStatus whereUserId($value)
 */
class FriendStatus extends Model
{
    const STATUS_ONLINE  = 1;
    const STATUS_OFFLINE = 0;

    protected $table = 'friends_status';

    public $timestamps = false;
}
