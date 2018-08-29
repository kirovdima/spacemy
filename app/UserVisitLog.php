<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * Class UserVisitLog
 * @package App
 */
class UserVisitLog extends Model
{
    protected $table = 'user_visit_logs';

    public $timestamps = false;

    /**
     * @param int $user_id
     * @param int|null $person_id
     *
     * @return array
     *
     * [
     *  'person_id' => '2018-07-27 12:00:00',
     *  ...
     * ]
     */
    public static function getLastVisitToFriendsStatistic(int $user_id, int $person_id = null)
    {
        $map_last_visit_function = function ($visit) {
            $pattern = '#' . ltrim(sprintf(URL::route('friends_statistic', ['person_id' => '%s'], false), '(\d+)'), '/') . '#';
            $matches = [];
            if (!($res = preg_match($pattern, $visit->action, $matches))) {
                return [];
            }
            return [$matches[1] => $visit->last_visit];
        };
        $friends_visits = DB::table('user_visit_logs')
            ->where('user_id' , '=', $user_id)
            ->where('action', 'like', ltrim(URL::route('friends_statistic', ['person_id' => $person_id ?: '%'], false), '/'))
            ->groupBy('action')
            ->select('action', DB::raw('MAX(created_at) AS last_visit'))
            ->get()
            ->mapWithKeys($map_last_visit_function)
            ->toArray();

        return $friends_visits;
    }
}
