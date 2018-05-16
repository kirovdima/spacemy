<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\StatisticService;

class VisitStatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function get($person_id)
    {
        $statistic_service = new StatisticService();

        return $statistic_service->getVisitsStatistic($person_id);
    }

    public function getFriendList($person_id)
    {
        $statistic_service = new StatisticService();

        return $statistic_service->getFriendsStatistic($person_id);
    }
}
