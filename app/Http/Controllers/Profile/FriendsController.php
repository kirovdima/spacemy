<?php

namespace App\Http\Controllers\Profile;

use App\Console\Commands\DeleteUserFriend;
use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\MongoModels\VkUser;
use App\Services\FriendListService;
use App\Services\Statistic\FriendsStatisticService;
use App\Services\Statistic\VisitStatisticService;
use App\UserFriend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;

class FriendsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('log');
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $friend_list_service = new FriendListService();
        $friend_list_service
            ->setOwnerId(Auth::user()->user_id)
            ->sortByLastName()
            ->separateByHasStatAndFirstLetter();

        $friends    = $friend_list_service->getFriends();
        $updated_at = $friend_list_service->getFormattedUpdatedAt();
        $friend_ids = $friend_list_service->getFriendIds();

        $visit_statistic_service = new VisitStatisticService();
        $visit_statistic_service
            ->setOwnerId(Auth::user()->user_id);
        $today_visit_statistic = $visit_statistic_service->getTodayAggregateStatistic();

        $friends_statistic_service = new FriendsStatisticService();
        $friends_statistic_service
            ->setOwnerId(Auth::user()->user_id)
            ->generateUnshowAggregateStatistic()
            ->formateAggregateStatistic()
        ;
        $unshow_friends_statistic = $friends_statistic_service->getStatistic();

        return [
            'vkFriends'      => $friends,
            'userFriendIds'  => $friend_ids,
            'todayStatistic' => $today_visit_statistic,
            'unshowFriendsStatistic' => $unshow_friends_statistic,
            'updated_at'     => $updated_at,
        ];
    }

    /**
     * @param int $person_id
     *
     * @return array
     */
    public function get(int $person_id)
    {
        $user = VkUser::getUser($person_id);
        $user_friend = UserFriend::getByUserIdAndPersonId(Auth::user()->user_id, $person_id);

        $friends_statistic_service = new FriendsStatisticService();
        $friends_statistic_service
            ->setOwnerId(Auth::user()->user_id)
            ->generateUnshowAggregateStatistic($person_id)
        ;
        $unshow_friend_statistic = $friends_statistic_service->getStatistic();

        return [
            'user'                    => $user,
            'is_statistic_exists'     => $user_friend ? true : false,
            'unshow_friend_statistic' => $unshow_friend_statistic,
            'start_monitoring_rus'    => $user_friend ? $user_friend->getFormattedCreatedAt() : null,
        ];
    }

    /**
     * @param int $person_id
     *
     * @return UserFriend
     * @throws \Exception
     */
    public function add(int $person_id)
    {
        $user_friend = UserFriend::getByUserIdAndPersonId(Auth::user()->user_id, $person_id);
        if ($user_friend) {
            throw new Exception(sprintf("friend '%s' already added", $person_id));
        }

        $user_friend = new UserFriend();
        $user_friend->user_id   = Auth::user()->user_id;
        $user_friend->friend_id = $person_id;
        $user_friend->save();

        return $user_friend;
    }

    /**
     * @param int $person_id
     *
     * @return array
     * @throws Exception
     */
    public function delete(int $person_id)
    {
        $user_friend = UserFriend::getByUserIdAndPersonId(Auth::user()->user_id, $person_id);
        if (!$user_friend) {
            throw new Exception(sprintf("user '%s' is not a friend", $person_id));
        }

        Bus::dispatch(
            new DeleteUserFriend($user_friend)
        );

        return [];
    }
}
