<?php

/**
 * (c) Stephen Pennell <ste@steyep.com>
 */

namespace App\Components\Traits;

/**
 * Provides methods for formatting time.
 */
trait FormatDurationTrait
{
    /**
     * Round a time up to the nearest specified minute interval.
     *
     * @param number $time
     *   Duration (in hours) to be formatted.
     * @param int    $interval
     *   (Optional) The interval (in minutes) by which the time should be
     *   rounded. Defaults to 15 minutes.
     *
     * @return number
     *   Returns the rounded time.
     */
    public static function roundToInterval($time, $interval = 15)
    {
        $time = static::convertHoursToMinutes($time);
        $rounded = ceil($time / $interval) * $interval;

        return static::convertMinutesToHours($rounded);
    }

    /**
     * Convert hours to minutes.
     *
     * @param number $hours
     *   The hours to be converted to minutes.
     *
     * @return number
     *   Returns the minute equivalent to the $hours provided.
     */
    public static function convertHoursToMinutes($hours)
    {
        return $hours * 60;
    }

    /**
     * Convert minutes to hours.
     *
     * @param number $minutes
     *   The minutes to be converted to hours.
     *
     * @return number
     *   Returns the hour equivalent to the $minutes provided.
     */
    public static function convertMinutesToHours($minutes)
    {
        return $minutes / 60;
    }
}
