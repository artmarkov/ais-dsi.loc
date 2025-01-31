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

    /**
     * Перевод секунд в академические часы
     * @param $astr_hour
     * @return float|int
     */
    public static function astr2academ($astr_hour)
    {
        switch (true) {
            case ($astr_hour == 0):
                $academ_hour = 0;
                break;
            case in_array($astr_hour, range(525, 825)):
                $academ_hour = 0.25;
                break;
            case in_array($astr_hour, range(1200, 1500)):
                $academ_hour = 0.5;
                break;
            case in_array($astr_hour, range(1875, 2325)):
                $academ_hour = 0.75;
                break;
            case in_array($astr_hour, range(2550, 2850)):
                $academ_hour = 1;
                break;
            case in_array($astr_hour, range(3225, 3525)):
                $academ_hour = 1.25;
                break;
            case in_array($astr_hour, range(3750, 4350)):
                $academ_hour = 1.5;
                break;
            case in_array($astr_hour, range(4425, 5025)):
                $academ_hour = 1.75;
                break;
            case in_array($astr_hour, range(5400, 5700)):
                $academ_hour = 2;
                break;
            case in_array($astr_hour, range(6000, 6375)):
                $academ_hour = 2.25;
                break;
            case in_array($astr_hour, range(6450, 7050)):
                $academ_hour = 2.5;
                break;
             case in_array($astr_hour, range(7125, 7725)):
                $academ_hour = 2.75;
                break;
            case in_array($astr_hour, range(7800, 8400)):
                $academ_hour = 3;
                break;
            case in_array($astr_hour, range(8475, 9075)):
                $academ_hour = 3.25;
                break;
            case in_array($astr_hour, range(9150, 9750)):
                $academ_hour = 3.5;
                break;
            case in_array($astr_hour, range(9825, 10425)):
                $academ_hour = 3.75;
                break;
            case in_array($astr_hour, range(10500, 11100)):
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
        $m = date('m', $timestamp);
        $d = date('d', $timestamp);
        $y = date('Y', $timestamp);

        return [mktime(0, 0, 0, $m, $d, $y), mktime(23, 59, 59, $m, $d, $y)];
    }
}