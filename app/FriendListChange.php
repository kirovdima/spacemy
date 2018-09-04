<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FriendListChange extends Model
{
    const STATUS_ADD    = 'add';
    const STATUS_DELETE = 'delete';

    protected $table = 'friends_list_change';

    public $timestamps = false;

    public static function getByUserId($user_id)
    {
        $changes = self::query()
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $changes;
    }
}
