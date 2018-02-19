<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FriendStatus extends Model
{
    const STATUS_ONLINE  = 1;
    const STATUS_OFFLINE = 0;

    protected $table = 'friends_status';

    public $timestamps = false;
}
