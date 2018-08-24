<?php

namespace App\Services;

use App\FriendListChange;
use App\FriendsStatus;
use App\MongoModels\VkUser;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

class StatisticService
{
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

        return ['statistic' => $statistic, 'labels' => $labels, 'data' => $data];
    }
}
