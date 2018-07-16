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

    public function getVisitsStatistic($person_id, $period)
    {
        switch ($period) {
            case 'day':
                $group_date_format = 'date_format(created_at, "%Y-%m-%d %H:00:00")';
                $limit = 24; // 24 часа
                break;
            case 'week':
                // группируем по 6 часов
                $group_date_format = 'date_add(
		            date_format(created_at, "%Y-%m-%d"), 
		                interval (
			                case 
                                when hour(created_at) < 6 then 0 
                                when hour(created_at) between 6 and 11 then 6
                                when hour(created_at) between 12 and 17 then 12
                                when hour(created_at) >= 18 then 18
                            end
                        ) hour
	                )';
                $limit = 4 * 7; // (24 / 6) * дней в неделе
                break;
            case 'month':
            default:
                $group_date_format = 'date_format(created_at, "%Y-%m-%d")';
                $limit = 30;
                break;
        }

        $statistic = FriendsStatus::select([
                DB::raw($group_date_format . ' group_date'),
                DB::raw('sum(status)*100/count(*) frequent')
            ])
            ->where('user_id', $person_id)
            ->groupBy('group_date')
            ->orderBy('group_date', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
        $statistic = array_reverse($statistic);

        Date::setLocale('ru');

        $labels = array_map(function ($item) {
            $timestamp = strtotime($item['group_date']);
            if (date('G', $timestamp) == 0) {
                return Date::createFromTimestamp($timestamp)->format('j F');
            } else {
                return date('G', $timestamp);
            }
        }, $statistic);
        $data   = array_map(function ($item) { return $item['frequent']; }, $statistic);

        return ['labels' => $labels, 'data' => $data];
    }
}
