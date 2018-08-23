<?php

namespace App\Services;

use App\FriendsList;
use App\FriendsStatus;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

class StatisticService
{
    public function getFriendsStatistic($person_id)
    {
        $friends_list = FriendsList::getByUserId($person_id);
        if (!$friends_list) {
            return ['first_friends_count' => null, 'friend_list_change' => null];
        }

        $first_friends = array_shift($friends_list);
        $first_friends = unserialize($first_friends['friends']);

        $prev_friends = $first_friends;
        $friends_list_change = [];
        foreach ($friends_list as $friends_item) {
            $friends = unserialize($friends_item['friends']);

            $friends_diff_function = function ($item1, $item2) {
                if ($item1['id'] > $item2['id']) {
                    return 1;
                } elseif ($item1['id'] < $item2['id']) {
                    return -1;
                } else {
                    return 0;
                }
            };

            Date::setLocale('ru');

            $change = [];
            if ($add_friends = array_udiff($friends, $prev_friends, $friends_diff_function)) {
                $change['add'] = $add_friends;
            } elseif ($delete_friends = array_udiff($prev_friends, $friends, $friends_diff_function)) {
                $change['delete'] = $delete_friends;
            }
            $change['date'] = Date::createFromFormat('Y-m-d H:i:s', $friends_item['created_at'])->format('j F');
            $friends_list_change[] = $change;

            $prev_friends = $friends;
        }

        return ['first_friends_count' => count($first_friends), 'friends_list_change' => array_reverse($friends_list_change)];
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

        Date::setLocale('ru');

        $labels = array_map(function ($date) use ($period) {
            $timestamp = strtotime($date);
            if (date('G', $timestamp) == 0) {
                if ($period == 'day') {
                    return date('G', $timestamp);
                } elseif ($period == 'week') {
                    return Date::createFromTimestamp($timestamp)->format('l');
                } else {
                    return Date::createFromTimestamp($timestamp)->format('j F');
                }
            } else {
                return date('G', $timestamp);
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

        return ['statistic' => $statistic, 'labels' => $labels, 'data' => $data];
    }
}
