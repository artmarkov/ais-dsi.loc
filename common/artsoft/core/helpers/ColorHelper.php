<?php

namespace artsoft\helpers;

/**
 * Class ColorHelper
 * @package artsoft\helpers
 */
class ColorHelper
{
    /**
     * Преобразование HEX в RGB
     *
     * @parm string $hex          Цвет
     * @parm bool $return_string  Результат в виде строки или массива
     * @return array|string|bool  В случаи ошибки false
     */
    public static function hex2rgb($hex, $return_string = false)
    {
        if (!preg_match('/#(([a-fA-F0-9]{3}){1,2}|([a-fA-F0-9]{4}){1,2})\b/', $hex)) {
            throw new \RuntimeException("Invalid color data, only hex format as #ffffff: " . $hex);
        }

        $hex = trim($hex, ' #');

        $size = strlen($hex);
        if ($size == 3 || $size == 4) {
            $parts = str_split($hex, 1);
            $hex = '';
            foreach ($parts as $row) {
                $hex .= $row . $row;
            }
        }

        $dec = hexdec($hex);
        $rgb = array();
        if ($size == 3 || $size == 6) {
            $rgb['red']   = 0xFF & ($dec >> 0x10);
            $rgb['green'] = 0xFF & ($dec >> 0x8);
            $rgb['blue']  = 0xFF & $dec;

            if ($return_string) {
                return 'rgb(' . implode(',', $rgb) . ')';
            }
        } elseif ($size == 4 || $size == 8) {
            $rgb['red']   = 0xFF & ($dec >> 0x16);
            $rgb['green'] = 0xFF & ($dec >> 0x10);
            $rgb['blue']  = 0xFF & ($dec >> 0x8);
            $rgb['alpha'] = 0xFF & $dec;

            if ($return_string) {
                $rgb['alpha'] = round(($rgb['alpha'] / (255 / 100)) / 100, 2);
                return 'rgba(' . implode(',', $rgb) . ')';
            } else {
                $rgb['alpha'] = 127 - ($rgb['alpha'] >> 1);
            }
        } else {
            return false;
        }

        return $rgb;
    }

    /**
     * @param $bgColor
     * Вычисляет цвет текста в зависимости от цвета фона (белый или черный) format hex
     * @return array
     */
    public static function calcColor($bgColor)
    {
        $rgb = self::hex2rgb($bgColor);
        $color = 1 - (0.299 * $rgb['red'] + 0.587 * $rgb['green'] + 0.114 * $rgb['blue']) / 255;

        return $color < 0.3 ? '#000000' : '#ffffff';
    }
}