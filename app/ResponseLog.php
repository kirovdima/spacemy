<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ResponseLog
 *
 * @mixin \Eloquent
 * @property string $method
 * @property string $parameters
 * @property int $status
 * @property int|null $error_code
 * @property string|null $error_msg
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ResponseLog whereErrorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ResponseLog whereErrorMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ResponseLog whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ResponseLog whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ResponseLog whereStatus($value)
 */
class ResponseLog extends Model
{
    const STATUS_OK    = 0;
    const STATUS_ERROR = 1;

    protected $table = 'response_logs';

    public $timestamps = false;
}
