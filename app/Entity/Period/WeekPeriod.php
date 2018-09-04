<?php

namespace App\Entity\Period;

use App\Jobs\CheckUserFriendsStatusJob;
use Jenssegers\Date\Date;

/**
 * Class WeekPeriod
 * @package App\Entity\Period
 */
class WeekPeriod implements PeriodInterface
{
    /**
     * @return string
     */
    public function getGroupDateFormat(): string
    {
        return 'date_format(created_at, "%Y-%m-%d 00:00:00")';
    }

    /**
     * @param string $start_date
     * @return string
     */
    public function getDateFrom(string $start_date): string
    {
        return date('Y-m-d H:i:s', strtotime('monday this week', strtotime($start_date)));
    }

    /**
     * @param string $start_date
     * @return string
     */
    public function getDateTo(string $start_date): string
    {
        return date('Y-m-d H:i:s', strtotime('monday next week', strtotime($start_date)));
    }

    /**
     * @param string $date
     * @param float $frequent
     * @return float
     */
    public function correctionFrequentForCurrentTimeInterval(string $date, float $frequent): float
    {
        if ($date == date('Y-m-d') . ' 00:00:00') {
            $frequent *= floor((date('H') * 3600 + date('i') * 60) / CheckUserFriendsStatusJob::TIME_INTERVAL) / (24 * 3600 / CheckUserFriendsStatusJob::TIME_INTERVAL);
        }
        return $frequent;
    }

    /**
     * @param string $date
     * @return string
     */
    public function increment(string $date): string
    {
        return date('Y-m-d H:i:s', strtotime('+1 day', strtotime($date)));
    }

    /**
     * @param $timestamp
     * @return string
     */
    public function formattingTimestampForLabel($timestamp): string
    {
        if ($timestamp > date('U') /*|| $timestamp < strtotime($start_monitoring_date) - 3600*/) {
            return '';
        }
        return Date::createFromTimestamp($timestamp)->format('l');
    }

    /**
     * @param $frequent
     * @return float
     */
    public function formattingFrequentForData($frequent): float
    {
        return round($frequent * 24, 1);
    }
}
