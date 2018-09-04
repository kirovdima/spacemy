<?php

namespace App\Entity\Period;

use App\Exceptions\Exception;

class PeriodFactory
{
    /**
     * @param string $type
     *
     * @return PeriodInterface
     * @throws Exception
     */
    public static function getPeriod(string $type): PeriodInterface
    {
        switch ($type) {
            case 'day':
                return new DayPeriod();
            case 'week':
                return new WeekPeriod();
            case 'month':
                return new MonthPeriod();
        }
    }
}
