<?php

namespace App\Entity\Period;

use App\Exceptions\Exception;

class PeriodFactory
{
    const DAY_PERIOD   = 'day';
    const WEEK_PERIOD  = 'week';
    const MONTH_PERIOD = 'month';

    public static $allowed_period = [
        self::DAY_PERIOD,
        self::WEEK_PERIOD,
        self::MONTH_PERIOD
    ];

    /**
     * @param string $type
     *
     * @return PeriodInterface
     * @throws Exception
     */
    public static function getPeriod(string $type): PeriodInterface
    {
        switch ($type) {
            case self::DAY_PERIOD:
                return new DayPeriod();
            case self::WEEK_PERIOD:
                return new WeekPeriod();
            case self::MONTH_PERIOD:
                return new MonthPeriod();
        }
    }
}
