<?php

namespace App\Entity\Period;

use App\Jobs\CheckUserFriendsStatusJob;

/**
 * Class DayPeriod
 * @package App\Entity\Period
 */
class DayPeriod implements PeriodInterface
{
    /**
     * @return string
     */
    public function getGroupDateFormat(): string
    {
        return 'date_format(created_at, "%Y-%m-%d %H:00:00")';
    }

    /**
     * @param string $start_date
     * @return string
     */
    public function getDateFrom(string $start_date): string
    {
        return date('Y-m-d H:i:s', strtotime($start_date));
    }

    /**
     * @param string $start_date
     * @return string
     */
    public function getDateTo(string $start_date): string
    {
        return date('Y-m-d H:i:s', strtotime($start_date) + 24 * 3600);
    }

    /**
     * @param string $date
     * @param float $frequent
     * @return float
     */
    public function correctionFrequentForCurrentTimeInterval(string $date, float $frequent): float
    {
        if ($date == date('Y-m-d H') . ':00:00') {
            $frequent *= (floor(date('i') * 60 / CheckUserFriendsStatusJob::TIME_INTERVAL) + 1) / (3600 / CheckUserFriendsStatusJob::TIME_INTERVAL);
        }
        return $frequent;
    }

    /**
     * @param string $date
     * @return string
     */
    public function increment(string $date): string
    {
        return date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($date)));
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
        if (date('G', $timestamp) == 0) {
            return date('G', $timestamp);
        } else {
            return date('G', $timestamp) . (date('G', $timestamp) % 6 == 0 ? ' часов' : '');
        }
    }

    /**
     * @param $frequent
     * @return float
     */
    public function formattingFrequentForData($frequent): float
    {
        return round($frequent * 60, 1);
    }
}
