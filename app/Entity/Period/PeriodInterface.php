<?php

namespace App\Entity\Period;

/**
 * Interface PeriodInterface
 * @package App\Entity\Period
 */
interface PeriodInterface
{
    /**
     * @return string
     */
    public function getGroupDateFormat() : string;

    /**
     * @param string $start_date
     * @return string
     */
    public function getDateFrom(string $start_date): string;

    /**
     * @param string $start_date
     * @return string
     */
    public function getDateTo(string $start_date): string;

    /**
     * @param string $date
     * @param float $frequent
     * @return float
     */
    public function correctionFrequentForCurrentTimeInterval(string $date, float $frequent): float;

    /**
     * @param string $date
     * @return string
     */
    public function increment(string $date): string;

    /**
     * @param $timestamp
     * @return string
     */
    public function formattingTimestampForLabel($timestamp): string;

    /**
     * @param $frequent
     * @return float
     */
    public function formattingFrequentForData($frequent): float;
}
