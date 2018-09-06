<?php

namespace App\Services\Statistic;

use App\Exceptions\Exception;
use App\FriendListChange;
use App\MongoModels\VkUser;
use App\UserFriend;
use App\UserVisitLog;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Jenssegers\Date\Date;

class FriendsStatisticService
{
    protected $owner_id = null;

    protected $statistic = null;

    /**
     * @param int $owner_id
     * @return $this
     */
    public function setOwnerId(int $owner_id)
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    public function getDetailStatistic(int $person_id)
    {
        if (null === $this->owner_id) {
            throw new Exception(sprintf("owner_id is null"));
        }

        $user_friend = UserFriend::getByUserIdAndPersonId($this->owner_id, $person_id);
        if (!$user_friend && !Auth::user()->isGuest()) {
            throw new Exception(sprintf("user '%s' is not a friend", $person_id));
        }

        $changes = FriendListChange::getByUserId($person_id);

        Date::setLocale('ru');
        $result = [];
        foreach ($changes as $change) {
            $friend = VkUser::getUser($change->friend_id);
            $result[] = [
                $change->status  => [
                    $friend
                ],
                'date'   => Date::createFromFormat('Y-m-d H:i:s', $change->created_at)->format('j F'),
            ];
        }

        return $result;
    }

    /**
     * @param int|null $person_id
     * @return $this
     * [
     *  'person_id' => [
     *      'add'    => '2',
     *      'delete' => '1',
     *  ],
     *  ...
     * ]
     */
    public function generateUnshowAggregateStatistic(int $person_id = null)
    {
        $last_visit_to_friends_statistic = UserVisitLog::getLastVisitToFriendsStatistic($this->owner_id, $person_id);

        $map_friends_function = function ($stat) {
            return [
                $stat->user_id => [
                    'add'    => $stat->add ?: null,
                    'delete' => $stat->delete ?: null,
                ]
            ];
        };
        $query = DB::table('user_friends AS uf')
            ->join('friends_list_change AS flc', 'uf.friend_id' , '=', 'flc.user_id')
            ->where('uf.user_id', '=', $this->owner_id);
        if ($person_id) {
            $query
                ->where('flc.user_id', '=', $person_id);
        }
        $this->statistic = $query
            ->where(function (Builder $query) use ($last_visit_to_friends_statistic, $person_id) {
                foreach ($last_visit_to_friends_statistic as $user_id => $last_visit) {
                    $query->orWhere([
                        ['flc.user_id', '=', $user_id],
                        ['flc.created_at', '>', $last_visit]
                    ]);
                }
                $query->orWhereNotIn('flc.user_id', array_keys($last_visit_to_friends_statistic));
            })
            ->groupBy('flc.user_id')
            ->select('flc.user_id', DB::raw('SUM(IF(flc.status="add",1,0)) AS `add`'), DB::raw('SUM(IF(flc.status="delete",1,0)) AS `delete`'))
            ->get()
            ->mapWithKeys($map_friends_function)
            ->toArray()
        ;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function formateAggregateStatistic()
    {
        if (null === $this->statistic) {
            throw new Exception(sprintf("statistic is not generate"));
        }

        $this->statistic = array_map(function ($statistic) {
            return [
                'add'    => $statistic['add']    ? ['count' => $statistic['add'], 'text' => Lang::choice(' друг| друга| друзей', $statistic['add'], [], 'ru') ] : null,
                'delete' => $statistic['delete'] ? ['count' => $statistic['delete'], 'text' => Lang::choice(' друг| друга| друзей', $statistic['delete'], [], 'ru') ] : null,
            ];
        }, $this->statistic);

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getStatistic()
    {
        if (null === $this->statistic) {
            throw new Exception(sprintf("statistic is not generate"));
        }

        return $this->statistic;
    }
}
