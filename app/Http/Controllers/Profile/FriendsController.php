<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\Vk;
use App\UserFriend;
use Illuminate\Support\Facades\Auth;

class FriendsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getAll()
    {
        $vkClient = new Vk();
        $vkFriends = $vkClient->getFriends(Auth::user());

        $userFriendIds = UserFriend::where('user_id', Auth::user()->user_id)
            ->pluck('friend_id')
            ->toArray();

        usort($vkFriends['items'], function ($friend1, $friend2) use ($userFriendIds) {
            if (in_array($friend1['id'], $userFriendIds) && !in_array($friend2['id'], $userFriendIds)) {
                return -1;
            } elseif (!in_array($friend1['id'], $userFriendIds) && in_array($friend2['id'], $userFriendIds)) {
                return 1;
            } else {
                return 0;
            }
        });

        return ['vkFriends' => $vkFriends, 'userFriendIds' => $userFriendIds];
    }

    public function get($person_id)
    {
        $vkClient = new Vk();
        $users = $vkClient->getUsers(Auth::user(), [$person_id]);
        $user = array_shift($users);

        $is_statistic_exists = !UserFriend::where('user_id', Auth::user()->user_id)
            ->where('friend_id', $person_id)
            ->get()
            ->isEmpty();

        return ['user' => $user, 'is_statistic_exists' => $is_statistic_exists];
    }

    public function add($person_id)
    {
        $user_id = Auth::user()->user_id;
        $res = UserFriend::where('user_id', $user_id)
            ->where('friend_id', $person_id)
            ->exists();
        if ($res) {
            throw new \Exception(sprintf("[%s:%s] friends %s already added", __CLASS__, __METHOD__, $person_id));
        }

        $userFriend = new UserFriend();
        $userFriend->user_id   = $user_id;
        $userFriend->friend_id = $person_id;
        $userFriend->save();

        return [];
    }

    public function delete($person_id)
    {
        $user_id = Auth::user()->user_id;
        UserFriend::where('user_id', $user_id)
            ->where('friend_id', $person_id)
            ->delete();

        return [];
    }
}
