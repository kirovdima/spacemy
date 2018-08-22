<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\StatisticService;

class VisitStatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('log');
    }

    public function get($person_id, $period, $start_date)
    {
        $statistic_service = new StatisticService();

        return $statistic_service->getVisitsStatistic($person_id, $period, $start_date);
    }

    public function getFriendList($person_id)
    {
        $statistic_service = new StatisticService();

        return $statistic_service->getFriendsStatistic($person_id);
    }
}
