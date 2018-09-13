<?php

namespace App\Http\Controllers\Profile;

use App\Console\Commands\DeleteUserFriend;
use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddPersonRequest;
use App\Http\Requests\DeletePersonRequest;
use App\Http\Requests\GetPersonRequest;
use App\MongoModels\VkUser;
use App\Services\FriendListService;
use App\Services\Statistic\FriendsStatisticService;
use App\Services\Statistic\VisitStatisticService;
use App\UserFriend;
use Illuminate\Http\Response;
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
     * @param GetPersonRequest $request
     * @return array
     */
    public function get(GetPersonRequest $request)
    {
        $person_id = $request->route('id');

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
            'is_statistic_exists'     => (bool) $user_friend || Auth::user()->isGuest(),
            'unshow_friend_statistic' => $unshow_friend_statistic,
            'start_monitoring_rus'    => $user_friend ? $user_friend->getFormattedCreatedAt() : null,
        ];
    }

    /**
     * @param AddPersonRequest $request
     * @return $this|UserFriend
     * @throws Exception
     */
    public function add(AddPersonRequest $request)
    {
        $person_id = $request->route('id');
        if (Auth::user()->watchingPersonsCount() >= 5 && Auth::user()->user_id != 15262661) {
            return response(['error_message' => 'persons max count'])
                ->setStatusCode(Response::HTTP_PAYMENT_REQUIRED);
        }

        $user_friend = new UserFriend();
        $user_friend->user_id   = Auth::user()->user_id;
        $user_friend->friend_id = $person_id;
        $user_friend->save();

        return $user_friend;
    }

    /**
     * @param DeletePersonRequest $request
     * @return array
     * @throws Exception
     */
    public function delete(DeletePersonRequest $request)
    {
        $person_id = $request->route('id');

        $user_friend = UserFriend::getByUserIdAndPersonId(Auth::user()->user_id, $person_id);
        Bus::dispatch(
            new DeleteUserFriend($user_friend)
        );

        return [];
    }
}
