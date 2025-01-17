<?php
namespace artsoft\helpers;

class Schedule
{
// определение в секундах значения академического времени
    public static function academ2astr($academ_hour)
    {
        switch ($academ_hour) {
            case '0':
                $astr_hour = 0;
                break;
            case '0.25':
                $astr_hour = 750;
                break;
            case '0.5':
                $astr_hour = 1500;
                break;
            case '0.75':
                $astr_hour = 2100;
                break;
            case '1':
                $astr_hour = 2700;
                break;
            case '1.25':
                $astr_hour = 3300;
                break;
            case '1.5':
                $astr_hour = 4200;
                break;
            case '2':
                $astr_hour = 5400;
                break;
            case '2.5':
                $astr_hour = 6900;
                break;
            case '3':
                $astr_hour = 8100;
                break;
            case '4':
                $astr_hour = 10800;
                break;
            case '4.5':
                $astr_hour = 12150;
                break;
            default:
                $astr_hour = round(($academ_hour * 2700), 2);
        };

        return $astr_hour;
    }

// определение в секундах значения академического времени
//    public static function astr2academ($astr_hour)
//    {
//        switch (true) {
//            case ($astr_hour == 0):
//                $academ_hour = 0;
//                break;
//            case in_array($astr_hour, range(750, 750)):
//                $academ_hour = 0.25;
//                break;
//            case in_array($astr_hour, range(1200, 1499)):
//                $academ_hour = 0.5;
//                break;
//            case in_array($astr_hour, range(1500, 1799)):
//                $academ_hour = 0.6;
//                break;
//            case in_array($astr_hour, range(1800, 1889)):
//                $academ_hour = 0.7;
//                break;
//            case in_array($astr_hour, range(1890, 2099)):
//                $academ_hour = 0.8;
//                break;
//            case in_array($astr_hour, range(2100, 2400)):
//                $academ_hour = 0.9;
//                break;
//            case in_array($astr_hour, range(2700, 2700)):
//                $academ_hour = 1;
//                break;
//            case in_array($astr_hour, range(3300, 3300)):
//                $academ_hour = 1.2;
//                break;
//            case in_array($astr_hour, range(3360, 3420)):
//                $academ_hour = 1.25;
//                break;
//            case in_array($astr_hour, range(3510, 3620)):
//                $academ_hour = 1.3;
//                break;
//            case in_array($astr_hour, range(3900, 4500)):
//                $academ_hour = 1.5;
//                break;
//            case in_array($astr_hour, range(4800, 4800)):
//                $academ_hour = 1.8;
//                break;
//            case in_array($astr_hour, range(5400, 5400)):
//                $academ_hour = 2;
//                break;
//            case in_array($astr_hour, range(6900, 6900)):
//                $academ_hour = 2.5;
//                break;
//            case in_array($astr_hour, range(8100, 8400)):
//                $academ_hour = 3;
//                break;
//            default:
//                $academ_hour = round(($astr_hour / 2700), 1);
//        };
//
//        return $academ_hour;
//    }
    public static function astr2academ($astr_hour)
    {
        switch (true) {
            case ($astr_hour == 0):
                $academ_hour = 0;
                break;
            case in_array($astr_hour, range(750, 750)):
                $academ_hour = 0.25;
                break;
            case in_array($astr_hour, range(1200, 1500)):
                $academ_hour = 0.5;
                break;
            case in_array($astr_hour, range(2100, 2400)):
                $academ_hour = 0.75;
                break;
            case in_array($astr_hour, range(2700, 2700)):
                $academ_hour = 1;
                break;
            case in_array($astr_hour, range(3300, 3420)):
                $academ_hour = 1.25;
                break;
            case in_array($astr_hour, range(3900, 4500)):
                $academ_hour = 1.5;
                break;
            case in_array($astr_hour, range(5400, 5700)):
                $academ_hour = 2;
                break;
            case in_array($astr_hour, range(6000, 6375)):
                $academ_hour = 2.25;
                break;
            case in_array($astr_hour, range(6600, 6900)):
                $academ_hour = 2.5;
                break;
            case in_array($astr_hour, range(8100, 8400)):
                $academ_hour = 3;
                break;
            default:
                $academ_hour = round(($astr_hour / 2700), 1);
        };

        return $academ_hour;
    }
    public static function encodeTime($value)
    {
        $t = explode(":", $value);
        return  $value ? mktime($t[0], $t[1], 0, 1, 1, 70) : '';

    }

    public static function decodeTime($value)
    {
        return  $value ? date('H:i', $value) : '';
    }

    public static function getWeekDay($day, $mon, $year)
    {
        $week_day = date("w", mktime(0, 0, 0, $mon, $day, $year));
        return $week_day != 0 ? $week_day : 7;
    }

    public static function timestamp2WeekDay($timestamp)
    {
        $week_day = date("w", $timestamp);
        return $week_day != 0 ? $week_day : 7;
    }

    public static function getWeekNum($day, $mon, $year)
    {
        return ((int)(($day + date("w", mktime(0, 0, 0, $mon, 1, $year)) - 2) / 7)) + 1;
    }

    public static function timestamp2WeekNum($timestamp)
    {
        $day = date('d', $timestamp);
        return ((int)(($day + date("w", $timestamp) - 2) / 7)) + 1;
    }


    /**
     * @param bool $timestamp
     * @return array
     */
    public static function getStartEndDay($timestamp = false)
    {
        $timestamp = $timestamp ? $timestamp : time();
        $m = date('m', $timestamp);
        $d = date('d', $timestamp);
        $y = date('Y', $timestamp);

        return [mktime(0, 0, 0, $m, $d, $y), mktime(23, 59, 59, $m, $d, $y)];
    }
}