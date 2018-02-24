<?php

namespace App\Http\Controllers\Profile;

use App\FriendStatus;
use App\Http\Controllers\Controller;
use App\Services\Vk;

class VisitStatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function get($person_id)
    {
        $vkClient = new Vk();
        $users = $vkClient->request(
            'users.get',
            \Auth::user()->access_token,
            [
                'user_ids' => $person_id,
                'fields'   => 'first_name,last_name,photo_50',
            ]
        );

        $user = array_shift($users);

        $statistic = FriendStatus::getModel()
            ->where('user_id', $person_id)
            ->orderBy('created_at', 'desc')
            ->limit(48)
            ->get()
            ->toArray();
        $statistic = array_reverse($statistic);

        $labels = array_map(function ($item) { return date('H:i', strtotime($item['created_at'])); }, $statistic);
        $data   = array_map(function ($item) { return $item['status']; }, $statistic);

        return ['labels' => $labels, 'data' => $data, 'user' => $user];
    }
}
