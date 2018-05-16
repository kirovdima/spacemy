<?php

namespace App\Http\Controllers\Profile;

use App\Console\Commands\DeleteUserFriend;
use App\Http\Controllers\Controller;
use App\Services\Vk;
use App\UserFriend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;

class FriendsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $vkClient = new Vk();
        $vk_friends = $vkClient->getFriends(Auth::user());

        $user_friend_ids = UserFriend::getFriendIds(Auth::user()->user_id);

        $friends_sort_function = function ($friend1, $friend2) use ($user_friend_ids) {
            if (in_array($friend1['id'], $user_friend_ids) && !in_array($friend2['id'], $user_friend_ids)) {
                return -1;
            } elseif (!in_array($friend1['id'], $user_friend_ids) && in_array($friend2['id'], $user_friend_ids)) {
                return 1;
            } else {
                return 0;
            }
        };
        usort($vk_friends['items'], $friends_sort_function);

        return ['vkFriends' => $vk_friends, 'userFriendIds' => $user_friend_ids];
    }

    /**
     * @param int $person_id
     * @return array
     */
    public function get($person_id)
    {
        $vkClient = new Vk();
        $users = $vkClient->getUsers(Auth::user(), [$person_id]);

        $is_user_friend_exists = UserFriend::isExists(Auth::user()->user_id, $person_id);

        return [
            'user'                => array_shift($users),
            'is_statistic_exists' => $is_user_friend_exists
        ];
    }

    /**
     * @param int $person_id
     *
     * @return UserFriend
     * @throws \Exception
     */
    public function add($person_id)
    {
        $user_friend = UserFriend::isExists(Auth::user()->user_id, $person_id);
        if ($user_friend) {
            throw new \Exception(sprintf("[%s:%s] friends %s already added", __CLASS__, __METHOD__, $person_id));
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
     */
    public function delete($person_id)
    {
        $user_friend = UserFriend::getByUserIdAndPersonId(Auth::user()->user_id, $person_id);
        Bus::dispatch(
            new DeleteUserFriend($user_friend)
        );

        return [];
    }
}
