<?php
/**
 * File DateParams.php
 */

namespace Nerdery\Xbox\Bundle\DataBundle\Date;

/**
 * Class DateParams
 *
 * Performs simple date calculations
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\DataBundle\Date
 */
class DateParams
{
    /**
     * Gets the time tomorrow at midnight in UTC
     *
     * @return int
     */
    public function getTomorrowAtMidnight()
    {
        return strtotime('tomorrow UTC');
    }

    /**
     * Returns if the day is the weekend
     *
     * If no date is passed in, use the current date
     *
     * @param $date
     *
     * @return bool
     */
    public function isWeekend($date = null) {
        if (null === $date) {
            $date = date('Y-m-d');
        }

        return (date('N', strtotime($date)) >= 6);
    }
}