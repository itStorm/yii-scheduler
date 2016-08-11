<?php
namespace YiiScheduler\Models;

/**
 * Class DateTimeRepresent
 * @package YiiScheduler\Models
 */
class DateTimeRepresent
{
    const SECONDS_IN_DAY = 86399;

    const DAYS = [
        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17,
        18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31,
    ];

    const COMPARE_DAYS_BINARY_VALUES = [
        1  => 1,
        2  => 2,
        3  => 4,
        4  => 8,
        5  => 16,
        6  => 32,
        7  => 64,
        8  => 128,
        9  => 256,
        10 => 512,
        11 => 1024,
        12 => 2048,
        13 => 4096,
        14 => 8192,
        15 => 16384,
        16 => 32768,
        17 => 65536,
        18 => 131072,
        19 => 262144,
        20 => 524288,
        21 => 1048576,
        22 => 2097152,
        23 => 4194304,
        24 => 8388608,
        25 => 16777216,
        26 => 33554432,
        27 => 67108864,
        28 => 134217728,
        29 => 268435456,
        30 => 536870912,
        31 => 1073741824,
    ];

    const ALL_DAYS_AS_BINARY_VALUE = 2147483647;

    const WEEK_DAYS_NAMES = [
        'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday',
    ];

    const COMPARE_WEEK_DAYS_INDEXES = [
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        7 => 'sunday',
    ];

    const COMPARE_WEEK_DAYS_BINARY_VALUES = [
        'monday'    => 1,
        'tuesday'   => 2,
        'wednesday' => 4,
        'thursday'  => 8,
        'friday'    => 16,
        'saturday'  => 32,
        'sunday'    => 64,
    ];

    const ALL_WEEK_DAYS_BINARY_VALUE = 127;

    const MONTHS_NAMES = [
        'january', 'february', 'march', 'april', 'may', 'june',
        'july', 'august', 'september', 'october', 'november', 'december',
    ];

    const COMPARE_MONTHS_INDEXES = [
        1  => 'january',
        2  => 'february',
        3  => 'march',
        4  => 'april',
        5  => 'may',
        6  => 'june',
        7  => 'july',
        8  => 'august',
        9  => 'september',
        10 => 'october',
        11 => 'november',
        12 => 'december',
    ];

    const COMPARE_MONTHS_BINARY_VALUES = [
        'january'   => 1,
        'february'  => 2,
        'march'     => 4,
        'april'     => 8,
        'may'       => 16,
        'june'      => 32,
        'july'      => 64,
        'august'    => 128,
        'september' => 256,
        'october'   => 512,
        'november'  => 1024,
        'december'  => 2048,
    ];

    const ALL_MONTHS_BINARY_VALUE = 4095;

    /**
     * @param mixed $days
     * @return int
     */
    public static function daysToBinaryValue($days)
    {
        if (!isset($days)) {
            return null;
        } elseif ($days == '*') {
            return self::ALL_DAYS_AS_BINARY_VALUE;
        }

        $daysAsInt = 0;
        if (!is_array($days)) {
            $days = [$days];
        }

        foreach ($days as $day) {
            if (!empty(self::COMPARE_DAYS_BINARY_VALUES[$day])) {
                $daysAsInt += self::COMPARE_DAYS_BINARY_VALUES[$day];
            }
        }

        return $daysAsInt ?: null;
    }

    /**
     * @param mixed $weekDays
     * @return int|null
     */
    public static function weekdaysBinaryValue($weekDays)
    {
        if (!isset($weekDays)) {
            return null;
        } elseif ($weekDays == '*') {
            return self::ALL_WEEK_DAYS_BINARY_VALUE;
        }

        $weekDaysAsInt = 0;
        if (!is_array($weekDays)) {
            $weekDays = [$weekDays];
        }

        foreach ($weekDays as $weekDay) {
            if (!empty(self::COMPARE_WEEK_DAYS_BINARY_VALUES[$weekDay])) {
                $weekDaysAsInt += self::COMPARE_WEEK_DAYS_BINARY_VALUES[$weekDay];
            }
        }

        return $weekDaysAsInt ?: null;
    }

    /**
     * @param mixed $months
     * @return int
     */
    public static function monthsBinaryValue($months)
    {
        if (!isset($months)) {
            return null;
        } elseif ($months == '*') {
            return self::ALL_MONTHS_BINARY_VALUE;
        }

        $monthsAsInt = 0;
        if (!is_array($months)) {
            $months = [$months];
        }

        foreach ($months as $month) {
            if (!empty(self::COMPARE_MONTHS_BINARY_VALUES[$month])) {
                $monthsAsInt += self::COMPARE_MONTHS_BINARY_VALUES[$month];
            }
        }

        return $monthsAsInt ?: null;
    }

    /**
     * Return time in seconds from day begin
     * @param string|int $time Time in seconds from day start or time in 24-h format like '11:55:34'
     * @return int
     */
    public static function timeToSeconds($time)
    {
        // Check time format.
        $isCorrectTime = (is_numeric($time) && $time > 0 && $time <= DateTimeRepresent::SECONDS_IN_DAY)
            || preg_match('/^(([01]{1}\d{1})|(2[0-3]{1}))(:[0-5]{1}\d{1}){1,2}$/', $time);

        if (!$isCorrectTime) {
            return null;
        }

        // Move string time in seconds from day start
        if (!is_numeric($time)) {
            $date = explode(':', $time);
            $hours = $date[0];
            $minutes = $date[1];
            $seconds = isset($date[2]) ? $date[2] : 0;
            $time = $hours * 3600 + $minutes * 60 + $seconds;
        }

        return $time;
    }
}