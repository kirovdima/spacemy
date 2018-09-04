<?php

namespace App\Services\Statistic;

use App\Entity\Period\PeriodInterface;
use App\Exceptions\Exception;
use App\FriendsStatus;
use App\Jobs\CheckUserFriendsStatusJob;
use App\UserFriend;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

/**
 * Class VisitStatisticService
 * @package App\Services\Statistic
 */
class VisitStatisticService
{
    protected $owner_id = null;

    protected $person_id = null;

    protected $start_date = null;

    /**
     * @var PeriodInterface
     */
    protected $period = null;

    protected $user_friend = null;

    protected $statistic = [];

    public function __construct()
    {
    }

    /**
     * @param int $owner_id
     * @return $this
     */
    public function setOwnerId(int $owner_id)
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    /**
     * @param int $person_id
     * @return $this
     */
    public function setPersonId(int $person_id)
    {
        $this->person_id = $person_id;

        return $this;
    }

    /**
     * @param string $start_date
     * @return $this
     */
    public function setStartDate(string $start_date)
    {
        $this->start_date = $start_date;

        return $this;
    }

    /**
     * @param PeriodInterface $period
     * @return $this
     */
    public function setPeriod(PeriodInterface $period)
    {
        $this->period = $period;

        return $this;
    }

    protected function getGroupDateFormat(): string
    {
        if (!$this->period instanceof PeriodInterface) {
            throw new Exception(sprintf("period is incorrect"));
        }

        return $this->period->getGroupDateFormat();
    }

    protected function getDateFrom(): string
    {
        if (!$this->period instanceof PeriodInterface) {
            throw new Exception(sprintf("period is incorrect"));
        }
        if (null === $this->start_date) {
            throw new Exception(sprintf("start_date is null"));
        }

        return $this->period->getDateFrom($this->start_date);
    }

    protected function getDateTo(): string
    {
        if (!$this->period instanceof PeriodInterface) {
            throw new Exception(sprintf("period is incorrect"));
        }
        if (null === $this->start_date) {
            throw new Exception(sprintf("start_date is null"));
        }

        return $this->period->getDateTo($this->start_date);
    }

    /**
     * @return UserFriend
     * @throws Exception
     */
    public function getUserFriend()
    {
        if (null === $this->user_friend) {
            throw new Exception(sprintf("user_friend is null"));
        }

        return $this->user_friend;
    }

    public function init()
    {
        $this->user_friend = UserFriend::getByUserIdAndPersonId($this->owner_id, $this->person_id);

        $group_date_format = $this->getGroupDateFormat();
        $date_from         = $this->getDateFrom();
        $date_to           = $this->getDateTo();

        if (null === $this->person_id) {
            throw new Exception("person_id is null");
        }

        $this->statistic = FriendsStatus::select([
            DB::raw($group_date_format . ' group_date'),
            DB::raw('sum(status)/count(*) frequent')
        ])
            ->where('user_id', $this->person_id)
            ->whereBetween('created_at', [$date_from, $date_to])
            ->groupBy('group_date')
            ->orderBy('group_date', 'asc')
            ->get()
            ->mapWithKeys(function ($stat) { return [$stat['group_date'] => $stat['frequent']]; })
            ->toArray();

        return $this;
    }

    public function fillToEndOfPeriod()
    {
        $current_date = $this->getDateFrom();
        $date_to      = $this->getDateTo();
        do {
            if (!isset($this->statistic[$current_date])) {
                $this->statistic[$current_date] = 0;
            }

            $this->statistic[$current_date] = $this->period->correctionFrequentForCurrentTimeInterval($current_date, $this->statistic[$current_date]);

            $current_date = $this->period->increment($current_date);

        } while ($current_date < $date_to);

        return $this;
    }

    public function sortByDate()
    {
        ksort($this->statistic);

        return $this;
    }

    public function getLabels()
    {
        Date::setLocale('ru');
        $labels = array_map(function ($date) {
            return $this->period->formattingTimestampForLabel(strtotime($date));
        }, array_keys($this->statistic));

        return $labels;
    }

    public function getData()
    {
        $data = array_map(function ($frequent) {
            return $this->period->formattingFrequentForData($frequent);
        }, array_values($this->statistic));

        return $data;
    }

    /**
     * @return array
     * [
     *  'person_id' => '10ч 50мин'
     * ]
     *
     * @throws Exception
     */
    public function getTodayAggregateStatistic()
    {
        if (null === $this->owner_id) {
            throw new Exception(sprintf("owner_id is empty"));
        }

        $map_function = function ($stat) {
            $hours   = floor($stat->count * CheckUserFriendsStatusJob::TIME_INTERVAL / 3600);
            $minutes = round((($stat->count * CheckUserFriendsStatusJob::TIME_INTERVAL) % 3600) / 60);
            return [
                $stat->user_id => sprintf("%sч %sмин", $hours, $minutes)
            ];
        };
        $today_statistic = DB::table('user_friends AS uf')
            ->join('friends_status AS fs', 'uf.friend_id', '=', 'fs.user_id')
            ->where('uf.user_id' , '=', $this->owner_id)
            ->where(DB::raw('date(fs.created_at)'), '=', DB::raw('date(NOW())'))
            ->where('fs.status', '=', FriendsStatus::STATUS_ONLINE)
            ->groupBy('fs.user_id')
            ->select('fs.user_id', DB::raw('COUNT(*) AS count'))
            ->get()
            ->mapWithKeys($map_function)
            ->toArray()
        ;

        return $today_statistic;
    }
}
