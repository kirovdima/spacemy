<?php

namespace App\Http\Controllers\Profile;

use App\Entity\Period\PeriodFactory;
use App\Http\Controllers\Controller;
use App\Services\Statistic\FriendsStatisticService;
use App\Services\Statistic\VisitStatisticService;
use Illuminate\Support\Facades\Auth;

class VisitStatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('log');
    }

    public function get($person_id, $period, $start_date)
    {
        $period_instance = PeriodFactory::getPeriod($period);

        $visit_statistic_service = new VisitStatisticService();
        $visit_statistic_service
            ->setOwnerId(Auth::user()->user_id)
            ->setPersonId($person_id)
            ->setStartDate($start_date)
            ->setPeriod($period_instance)
            ->init()
            ->fillToEndOfPeriod()
            ->sortByDate()
        ;

        $labels = $visit_statistic_service->getLabels();
        $data   = $visit_statistic_service->getData();
        $start_monitoring_date = $visit_statistic_service->getUserFriend()->created_at;

        return ['labels' => $labels, 'data' => $data, 'start_monitoring_date' => $start_monitoring_date];
    }

    public function getFriendList($person_id)
    {
        $friends_statistic_service = new FriendsStatisticService();
        $friends_statistic_service
            ->setOwnerId(Auth::user()->user_id);

        $statistic = $friends_statistic_service->getDetailStatistic($person_id);

        return ['friends_list_change' => $statistic];
    }
}
