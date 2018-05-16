<?php

namespace App\Services;

use App\FriendsList;
use App\FriendsStatus;
use Illuminate\Support\Facades\DB;

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

            $change = [];
            if ($add_friends = array_udiff($friends, $prev_friends, $friends_diff_function)) {
                $change['add'] = $add_friends;
            } elseif ($delete_friends = array_udiff($prev_friends, $friends, $friends_diff_function)) {
                $change['delete'] = $delete_friends;
            }
            $friends_list_change[] = $change;

            $prev_friends = $friends;
        }

        return ['first_friends_count' => count($first_friends), 'friends_list_change' => array_reverse($friends_list_change)];
    }

    public function getVisitsStatistic($person_id)
    {
        $statistic = FriendsStatus::select([DB::raw('date_format(created_at, "%Y-%m-%d %H") dateH'), DB::raw('sum(status)*100/count(*) frequent')])
            ->where('user_id', $person_id)
            ->groupBy(DB::raw('date_format(created_at, "%Y-%m-%d %H")'))
            ->orderBy(DB::raw('date_format(created_at, "%Y-%m-%d %H")'), 'desc')
            ->limit(24)
            ->get()
            ->toArray();
        $statistic = array_reverse($statistic);

        $labels = array_map(function ($item) {
            $timestamp = strtotime($item['dateH'] . ':00:00');
            if (date('G', $timestamp) == 0) {
                return date('M j', $timestamp);
            } else {
                return date('G', $timestamp);
            }
        }, $statistic);
        $data   = array_map(function ($item) { return $item['frequent']; }, $statistic);

        return ['labels' => $labels, 'data' => $data];
    }
}
