<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\Vk;
use Illuminate\Support\Facades\DB;

class VisitStatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function get($person_id)
    {
        $statistic = DB::table('friends_status')
            ->select(DB::raw('date_format(created_at, "%Y-%m-%d %H") dateH'), DB::raw('sum(status)*100/count(*) frequent'))
            ->where('user_id', $person_id)
            ->groupBy(DB::raw('date_format(created_at, "%Y-%m-%d %H")'))
            ->orderBy(DB::raw('date_format(created_at, "%Y-%m-%d %H")'), 'desc')
            ->limit(24)
            ->get()
            ->toArray();
        $statistic = array_reverse($statistic);

        $labels = array_map(function ($item) {
            $timestamp = strtotime($item->dateH . ':00:00');
            if (date('G', $timestamp) == 0) {
                return date('M j', $timestamp);
            } else {
                return date('G', $timestamp);
            }
        }, $statistic);
        $data   = array_map(function ($item) { return $item->frequent; }, $statistic);

        return ['labels' => $labels, 'data' => $data];
    }
}
