<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\FriendsStatus
 *
 * @mixin \Eloquent
 * @property int $user_id
 * @property int $status
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\FriendsStatus whereUserId($value)
 */
class FriendsStatus extends Model
{
    const STATUS_ONLINE  = 1;
    const STATUS_OFFLINE = 0;

    protected $table = 'friends_status';

    public $timestamps = false;

    /**
     * @param $user_id
     * @return Model|null|object|static
     */
    public static function getStartMonitoringDateByUserId($user_id)
    {
        $first = DB::table('friends_status')
            ->where('user_id', '=', $user_id)
            ->orderBy('created_at', 'ASC')
            ->first();

        return $first ? $first->created_at : null;
    }
}
