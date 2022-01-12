<?php
namespace artsoft\helpers;

class Schedule
{
// определение в секундах значения академического времени
public static function academ_astr_hour($academ_hour)
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
        default:
            $astr_hour = round(($academ_hour * 2700), 2);
    };

    return $astr_hour;
}

// определение в секундах значения академического времени
public static function astr_academ_hour($astr_hour)
{
    switch ($astr_hour) {
        case '0':
            $academ_hour = 0;
            break;
        case '750':
            $academ_hour = 0.25;
            break;
        case '1500':
            $academ_hour = 0.5;
            break;
        case '2100':
            $academ_hour = 0.75;
            break;
        case '2700':
            $academ_hour = 1;
            break;
        case '3300':
            $academ_hour = 1.25;
            break;
        case '4200':
            $academ_hour = 1.5;
            break;
        case '5400':
            $academ_hour = 2;
            break;
        case '6900':
            $academ_hour = 2.5;
            break;
        case '8100':
            $academ_hour = 3;
            break;
        case '1380':
            $academ_hour = 0.5;
            break;
        case '1320':
            $academ_hour = 0.5;
            break;
        case '1260':
            $academ_hour = 0.5;
            break;
        case '1200':
            $academ_hour = 0.5;
            break;
        case '2400':
            $academ_hour = 0.75;
            break;
        case '3360':
            $academ_hour = 1.25;
            break;
        case '3420':
            $academ_hour = 1.25;
            break;
        case '4140':
            $academ_hour = 1.5;
            break;
        case '4080':
            $academ_hour = 1.5;
            break;
        case '4020':
            $academ_hour = 1.5;
            break;
        case '4260':
            $academ_hour = 1.5;
            break;
        case '4320':
            $academ_hour = 1.5;
            break;
        case '4380':
            $academ_hour = 1.5;
            break;
        case '4440':
            $academ_hour = 1.5;
            break;
        case '4500':
            $academ_hour = 1.5;
            break;
        case '8400':
            $academ_hour = 3;
            break;
        default:
            $academ_hour = round(($astr_hour / 2700), 2);
    };

    return $academ_hour;
}
}