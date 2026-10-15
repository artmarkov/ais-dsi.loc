<?php
namespace artsoft\helpers;

class Schedule
{
    /**
     * Перевод академических часов в секунды
     * @param $academ_hour
     * @return int
     */
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
            case '1.75':
                $astr_hour = 4800;
                break;
            case '2':
                $astr_hour = 5400;
                break;
            case '2.25':
                $astr_hour = 6000;
                break;
            case '2.5':
                $astr_hour = 6900;
                break;
            case '2.75':
                $astr_hour = 7500;
                break;
            case '3':
                $astr_hour = 8100;
                break;
            case '3.25':
                $astr_hour = 9000;
                break;
            case '3.5':
                $astr_hour = 9600;
                break;
            case '3.75':
                $astr_hour = 10200;
                break;
            case '4':
                $astr_hour = 10800;
                break;
            case '4.5':
                $astr_hour = 12000;
                break;
            default:
                $astr_hour = round(($academ_hour * 2700), 2);
        };

        return $astr_hour;
    }

    /**
     * Перевод секунд в академические часы
     * @param $astr_hour
     * @return float|int
     */
    public static function astr2academ($astr_hour)
    {
        $rate = 300;
        switch (true) {
            case ($astr_hour == 0):
                $academ_hour = 0;
                break;
            case in_array($astr_hour, range(0.25*2700-$rate, 0.25*2700+$rate)):
                $academ_hour = 0.25;
                break;
            case in_array($astr_hour, range(0.5*2700-$rate, 0.5*2700+$rate)):
                $academ_hour = 0.5;
                break;
            case in_array($astr_hour, range(0.75*2700-$rate, 0.75*2700+$rate)):
                $academ_hour = 0.75;
                break;
            case in_array($astr_hour, range(1*2700-$rate, 1*2700+$rate)):
                $academ_hour = 1;
                break;
            case in_array($astr_hour, range(1.25*2700-$rate, 1.25*2700+$rate)):
                $academ_hour = 1.25;
                break;
            case in_array($astr_hour, range(1.5*2700-$rate, 1.5*2700+$rate)):
                $academ_hour = 1.5;
                break;
            case in_array($astr_hour, range(1.75*2700-$rate, 1.75*2700+$rate)):
                $academ_hour = 1.75;
                break;
            case in_array($astr_hour, range(2*2700-$rate, 2*2700+$rate)):
                $academ_hour = 2;
                break;
            case in_array($astr_hour, range(2.25*2700-$rate, 2.25*2700+$rate)):
                $academ_hour = 2.25;
                break;
            case in_array($astr_hour, range(2.5*2700-$rate, 2.5*2700+$rate)):
                $academ_hour = 2.5;
                break;
             case in_array($astr_hour, range(2.75*2700-$rate, 2.75*2700+$rate)):
                $academ_hour = 2.75;
                break;
            case in_array($astr_hour, range(3*2700-$rate, 3*2700+$rate)):
                $academ_hour = 3;
                break;
            case in_array($astr_hour, range(3.25*2700-$rate, 3.25*2700+$rate)):
                $academ_hour = 3.25;
                break;
            case in_array($astr_hour, range(3.5*2700-$rate, 3.5*2700+$rate)):
                $academ_hour = 3.5;
                break;
            case in_array($astr_hour, range(3.75*2700-$rate, 3.75*2700+$rate)):
                $academ_hour = 3.75;
                break;
            case in_array($astr_hour, range(4*2700-$rate, 4*2700+$rate)):
                $academ_hour = 4;
                break;
            default:
                $academ_hour = round(($astr_hour / 2700), 1);
        };

        return $academ_hour;
    }

    /**
     * Преобразование часы:минуты в метку времени
     * @param $value
     * @return false|int|string
     */
    public static function encodeTime($value)
    {
        $t = explode(":", $value);
        return  $value ? mktime($t[0], $t[1], 0, 1, 1, 70) : '';

    }

    /**
     * Преобразование метки времени в часы:минуты
     * @param $value
     * @return false|string
     */
    public static function decodeTime($value)
    {
        return  $value ? date('H:i', $value) : '';
    }

    /**
     * Получение дня недели из параметров день, месяц, год
     * @param $day
     * @param $mon
     * @param $year
     * @return false|int|string
     */
    public static function getWeekDay($day, $mon, $year)
    {
        $week_day = date("w", mktime(0, 0, 0, $mon, $day, $year));
        return $week_day != 0 ? $week_day : 7;
    }

    /**
     * Извлечение дня недели из метки времени
     * @param $timestamp
     * @return false|int|string
     */
    public static function timestamp2WeekDay($timestamp)
    {
        $week_day = date("w", $timestamp);
        return $week_day != 0 ? $week_day : 7;
    }

    public static function getWeekNum($day, $mon, $year)
    {
        return ((int)(($day + date("w", mktime(0, 0, 0, $mon, 1, $year)) - 2) / 7)) + 1;
    }

    /**
     * Извлечение номера недели из метки времени
     * @param $timestamp
     * @return int
     */
    public static function timestamp2WeekNum($timestamp)
    {
        $day = date('d', $timestamp);
        return ((int)(($day + date("w", $timestamp) - 2) / 7)) + 1;
    }


    /**
     * Получение начала и окончания суток по метке времени
     * @param bool $timestamp
     * @return array
     */
    public static function getStartEndDay($timestamp = false)
    {
        $timestamp = $timestamp ? $timestamp : time();

        $beginOfDay = strtotime("today", $timestamp);
        $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;

        return [$beginOfDay, $endOfDay];
    }
}