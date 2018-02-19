<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseLog extends Model
{
    const STATUS_OK    = 0;
    const STATUS_ERROR = 1;

    protected $table = 'response_logs';

    public $timestamps = false;
}
