<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\UserFriend;

class FriendsController extends Controller
{
    public function index()
    {
        if (!\Auth::check()) {
            $oauth_url = config('app.vk.oauth_url');
            $redirect_uri = $oauth_url . '?'
                . 'client_id=' . config('app.vk.client_id')
                . '&redirect_uri=http://spacemy.ru/verify'
                . '&display=' . config('app.vk.display')
                . '&scope=' . config('app.vk.scope')
                . '&response_type=code'
                . '&v=' . config('app.vk.v')
            ;

            return redirect($redirect_uri);
        }
    }

    public function get()
    {
        $vkClient = new \App\Services\Vk();
        $access_token = \Auth::user()->access_token;
        $user_id = \Auth::user()->user_id;
        $vkFriends = $vkClient->request('friends.get', $access_token, ['fields' => 'id,first_name,last_name,photo_50']);
        if (!$vkFriends) {
            $vkFriends = [];
        }

        $userFriends = UserFriend::getModel()->where('user_id', $user_id)->get()->toArray();
        $userFriendIds = array_map(function ($userFriend) { return $userFriend['friend_id']; }, $userFriends);

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

    public function add($person_id)
    {
        $user_id = \Auth::user()->user_id;
        $res = UserFriend::getModel()
            ->where('user_id', $user_id)
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
        $user_id = \Auth::user()->user_id;
        UserFriend::getModel()
            ->where('user_id', $user_id)
            ->where('friend_id', $person_id)
            ->delete();

        return [];
    }
}
