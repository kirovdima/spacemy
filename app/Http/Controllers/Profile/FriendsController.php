<?php

namespace App\Http\Controllers\Profile;

use App\Console\Commands\DeleteUserFriend;
use App\Http\Controllers\Controller;
use App\MongoModels\VkFriend;
use App\MongoModels\VkUser;
use App\UserFriend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Jenssegers\Date\Date;

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
        $vk_friends = VkFriend::query()
            ->where('user_id', '=', Auth::user()->user_id)
            ->first()
            ->toArray();

        $user_friend_ids = UserFriend::getFriendIds(Auth::user()->user_id);

        $sort_by_last_name_function = function ($friend1, $friend2) use ($user_friend_ids) {
            if ($friend1['last_name'] < $friend2['last_name']) {
                return -1;
            } elseif ($friend1['last_name'] > $friend2['last_name']) {
                return 1;
            } else {
                return 0;
            }
        };
        usort($vk_friends['friends'], $sort_by_last_name_function);

        $friends = [];
        $friends['has_stat'] = [];
        foreach ($vk_friends['friends'] as $vk_friend) {
            if (in_array($vk_friend['id'], $user_friend_ids)) {
                $friends['has_stat'][] = $vk_friend;
            } else {
                $first_letter = mb_substr($vk_friend['last_name'], 0, 1);
                $friends[$first_letter][] = $vk_friend;
            }
        }

        Date::setLocale('ru');
        $updated_at = (new Date($vk_friends['updated_at']))->ago();

        return ['vkFriends' => $friends, 'userFriendIds' => $user_friend_ids, 'updated_at' => $updated_at];
    }

    /**
     * @param int $person_id
     * @return array
     */
    public function get($person_id)
    {
        $user = VkUser::query()
            ->where('id', '=', (int)$person_id)
            ->first()
            ->toArray();

        $is_user_friend_exists = UserFriend::isExists(Auth::user()->user_id, $person_id);

        return [
            'user'                => $user,
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
