<?php

namespace App\Services;

use App\FriendListChange;
use App\FriendsStatus;
use App\Jobs\CheckUserFriendsStatusJob;
use App\MongoModels\VkUser;
use App\UserFriend;
use App\UserVisitLog;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

class StatisticService
{
    /**
     * @param int      $user_id
     * @param int|null $person_id
     *
     * @return array
     * [
     *  'person_id' => '10ч 50мин'
     * ]
     */
    public static function getTodayFriendsVisitStatistic(int $user_id, int $person_id = null)
    {
        $map_function = function ($stat) {
            $hours   = floor($stat->count * CheckUserFriendsStatusJob::TIME_INTERVAL / 3600);
            $minutes = round((($stat->count * CheckUserFriendsStatusJob::TIME_INTERVAL) % 3600) / 60);
            return [
                $stat->user_id => sprintf("%sч %sмин", $hours, $minutes)
            ];
        };
        $today_statistic = DB::table('user_friends AS uf')
            ->join('friends_status AS fs', 'uf.friend_id', '=', 'fs.user_id')
            ->where('uf.user_id' , '=', $user_id)
            ->where(DB::raw('date(fs.created_at)'), '=', DB::raw('date(NOW())'))
            ->where('fs.status', '=', FriendsStatus::STATUS_ONLINE)
            ->groupBy('fs.user_id')
            ->select('fs.user_id', DB::raw('COUNT(*) AS count'))
            ->get()
            ->mapWithKeys($map_function)
            ->toArray()
        ;

        return $today_statistic;
    }

    /**
     * @param int      $user_id
     * @param int|null $person_id
     *
     * @return array
     * [
     *  'person_id' => [
     *      'add'    => '2',
     *      'delete' => '1',
     *  ],
     *  ...
     * ]
     */
    public static function getUnshowFriendsStatistic(int $user_id, int $person_id = null)
    {
        $last_visit_to_friends_statistic = UserVisitLog::getLastVisitToFriendsStatistic($user_id, $person_id);

        $map_friends_function = function ($stat) {
            return [
                $stat->user_id => [
                    'add'    => $stat->add ?: null,
                    'delete' => $stat->delete ?: null,
                ]
            ];
        };
        $query = DB::table('user_friends AS uf')
            ->join('friends_list_change AS flc', 'uf.friend_id' , '=', 'flc.user_id')
            ->where('uf.user_id', '=', $user_id);
        if ($person_id) {
            $query
                ->where('flc.user_id', '=', $person_id);
        }
        $unshow_friends_statistic = $query
            ->where(function (Builder $query) use ($last_visit_to_friends_statistic, $person_id) {
                foreach ($last_visit_to_friends_statistic as $user_id => $last_visit) {
                    $query->orWhere([
                        ['flc.user_id', '=', $user_id],
                        ['flc.created_at', '>', $last_visit]
                    ]);
                }
                $query->orWhereNotIn('flc.user_id', array_keys($last_visit_to_friends_statistic));
            })
            ->groupBy('flc.user_id')
            ->select('flc.user_id', DB::raw('SUM(IF(flc.status="add",1,0)) AS `add`'), DB::raw('SUM(IF(flc.status="delete",1,0)) AS `delete`'))
            ->get()
            ->mapWithKeys($map_friends_function)
            ->toArray()
        ;

        return $unshow_friends_statistic;
    }

    public function getFriendsStatistic($person_id)
    {
        $changes = FriendListChange::query()
            ->where('user_id', $person_id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();

        $result = [];
        Date::setLocale('ru');

        foreach ($changes as $change) {
            $friend_id = $change['friend_id'];
            $status    = $change['status'];

            $friend = VkUser::query()
                ->where('id', '=', (int)$friend_id)
                ->first()
                ->toArray();
            $result[] = [
                $status  => [
                    $friend
                ],
                'date'   => Date::createFromFormat('Y-m-d H:i:s', $change['created_at'])->format('j F'),
            ];
        }

        return ['friends_list_change' => $result];
    }

    public function getVisitsStatistic($person_id, $period, $start_date)
    {
        switch ($period) {
            case 'day':
                $group_date_format = 'date_format(created_at, "%Y-%m-%d %H:00:00")';
                $date_from = date('Y-m-d H:i:s', strtotime($start_date));
                $date_to   = date('Y-m-d H:i:s', strtotime($start_date) + 24 * 3600);
                break;
            case 'week':
                $group_date_format = 'date_format(created_at, "%Y-%m-%d 00:00:00")';
                $date_from = date('Y-m-d H:i:s', strtotime('monday this week', strtotime($start_date)));
                $date_to   = date('Y-m-d H:i:s', strtotime('monday next week', strtotime($start_date)));
                break;
            case 'month':
            default:
                $group_date_format = 'date_format(created_at, "%Y-%m-%d 00:00:00")';
                $date_from = date('Y-m-d H:i:s', strtotime('first day of this month', strtotime($start_date)));
                $date_to   = date('Y-m-d H:i:s', strtotime('first day of next month', strtotime($start_date)));
                break;
        }

        $statistic = FriendsStatus::select([
                DB::raw($group_date_format . ' group_date'),
                DB::raw('sum(status)/count(*) frequent')
            ])
            ->where('user_id', $person_id)
            ->whereBetween('created_at', [$date_from, $date_to])
            ->groupBy('group_date')
            ->orderBy('group_date', 'asc')
            ->get()
            ->mapWithKeys(function ($stat) { return [$stat['group_date'] => $stat['frequent']]; })
            ->toArray();

        $current_date = $date_from;
        do {
            if (!isset($statistic[$current_date])) {
                $statistic[$current_date] = 0;
            }
            switch ($period) {
                case 'day':
                    if ($current_date == date('Y-m-d H') . ':00:00') {
                        $statistic[$current_date] *= (floor(date('i') * 60 / CheckUserFriendsStatusJob::TIME_INTERVAL) + 1) / (3600 / CheckUserFriendsStatusJob::TIME_INTERVAL);
                    }
                    break;
                case 'week':
                case 'month':
                    if ($current_date == date('Y-m-d') . ' 00:00:00') {
                        $statistic[$current_date] *= floor((date('H') * 3600 + date('i') * 60) / CheckUserFriendsStatusJob::TIME_INTERVAL) / (24 * 3600 / CheckUserFriendsStatusJob::TIME_INTERVAL);
                    }
                    break;
            }
            switch ($period) {
                case 'day':
                    $current_date = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($current_date)));
                    break;
                case 'week':
                    $current_date = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($current_date)));
                    break;
                case 'month':
                    $current_date = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($current_date)));
                    break;
            }
        } while ($current_date < $date_to);
        ksort($statistic);

        $user_friend = UserFriend::getByUserIdAndPersonId(Auth::user()->user_id, $person_id);
        $start_monitoring_date = $user_friend->created_at;

        Date::setLocale('ru');
        $labels = array_map(function ($date) use ($period, $start_monitoring_date) {
            $timestamp = strtotime($date);
            if ($timestamp > date('U') /*|| $timestamp < strtotime($start_monitoring_date) - 3600*/) {
                return '';
            }
            if (date('G', $timestamp) == 0) {
                if ($period == 'day') {
                    return date('G', $timestamp);
                } elseif ($period == 'week') {
                    return Date::createFromTimestamp($timestamp)->format('l');
                } else {
                    return Date::createFromTimestamp($timestamp)->format('j F');
                }
            } else {
                return date('G', $timestamp) . (date('G', $timestamp) % 6 == 0 ? ' часов' : '');
            }
        }, array_keys($statistic));

        $data = array_map(function ($frequent) use ($period) {
            switch ($period) {
                case 'day':
                    return round($frequent * 60, 1);
                    break;
                case 'week':
                case 'month':
                    return round($frequent * 24, 1);
                    break;
            }
        }, array_values($statistic));

        return ['labels' => $labels, 'data' => $data, 'start_monitoring_date' => $start_monitoring_date];
    }
}
