<?php

namespace artsoft\helpers;

use Yii;

/**
 * A lot of useful and useless stuff
 *
 */
class ArtHelper
{
    /**
     * Return old good slug
     *
     * @var array
     */
    public static $transliteration = [
        // Latin symbols
        '©' => '(c)',
        // Greek
        'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H',
        'Θ' => '8',
        'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O',
        'Π' => 'P',
        'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS',
        'Ω' => 'W',
        'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W',
        'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h',
        'θ' => '8',
        'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o',
        'π' => 'p',
        'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps',
        'ω' => 'w',
        'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w',
        'ς' => 's',
        'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
        // Turkish
        'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
        'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
        // Russian
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo',
        'Ж' => 'Zh',
        'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O',
        'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H',
        'Ц' => 'C',
        'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu',
        'я' => 'ya',
        // Ukrainian
        'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
        'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
        // Czech
        'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T',
        'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't',
        'ů' => 'u',
        'ž' => 'z',
        // Polish
        'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',
        // Latvian
        'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L',
        'Ņ' => 'N',
        'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
        'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l',
        'ņ' => 'n',
        'š' => 's', 'ū' => 'u', 'ž' => 'z',
        //Vietnamese
        'Ấ' => 'A', 'Ầ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A',
        'Ắ' => 'A', 'Ằ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A',
        'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O',
        'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O',
        'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E',
        'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
        'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
        'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
        'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
        'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e'
    ];

    /**
     * Return old good slug
     *
     * @param string $string
     * @param string $replacement
     * @param bool $lowercase
     *
     * @return string
     */
    public static function slug($string, $replacement = '-', $lowercase = true)
    {
        if (extension_loaded('intl') === true) {
            $options = 'Any-Latin; NFKD';
            $string = transliterator_transliterate($options, $string);

            $string = preg_replace('/[^a-zA-Z0-9=\s—–-]+/u', '', $string);
            $string = preg_replace('/[=\s—–-]+/u', $replacement, $string);
        } else {
            $string = str_replace(array_keys(static::$transliteration), static::$transliteration, $string);
            $string = preg_replace('/[^\p{L}\p{Nd}]+/u', $replacement, $string);
        }

        $string = trim($string, $replacement);

        return $lowercase ? strtolower($string) : $string;
    }

    /**
     * Find real user IP
     *
     * @return string|null
     */
    public static function getRealIp()
    {
        $ip = null;

        if (php_sapi_name() == 'cli') {
            return $ip;
        } else {
            $client = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote = @$_SERVER['REMOTE_ADDR'];

            if (filter_var($client, FILTER_VALIDATE_IP)) {
                $ip = $client;
            } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
                $ip = $forward;
            } else {
                $ip = $remote;
            }

            return $ip;
        }
    }

    /**
     * Check by string class name if class implements interface
     *
     * @param string $classNamespace
     * @param string $interface
     * @return boolean
     */
    public static function isImplemented($classNamespace, $interface)
    {
        $interfaces = class_implements($classNamespace);
        return isset($interfaces[$interface]);
    }

    /**
     * Remove file or directory
     *
     * @param string $path
     * @return boolean
     */
    public static function recursiveDelete($path)
    {
        if (is_file($path)) {
            return @unlink($path);
        } elseif (is_dir($path)) {
            $scan = glob(rtrim($path, '/') . '/*');
            foreach ($scan as $index => $newPath) {
                self::recursiveDelete($newPath);
            }
            return @rmdir($path);
        }
    }

    /**
     * @param string $format
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function getMonthsList()
    {
        $monthAr = [
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря'
        ];

        return $monthAr;
    }

    public static function getMonthsNominativeList()
    {
        $monthAr = [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        ];

        return $monthAr;
    }

    /**
     * @param $val
     * @return mixed|null
     */
    public static function getMonthsNominativeValue($val)
    {
        $list = self::getMonthsNominativeList();

        return isset($list[$val]) ? $list[$val] : null;
    }

    /**
     * @param int $start
     * @return array
     */
    public static function getStudyYearsList($start = 10)
    {
        $list = [];
        $year = self::getStudyYearDefault();
        for ($i = $year + 1; $i > ($year - $start); $i--) {
            $list[$i] = $i . '/' . ($i + 1);
        }

        return $list;
    }

    /**
     * @param $val
     * @return mixed|null
     */
    public static function getStudyYearsValue($val)
    {
        $list = self::getStudyYearsList();

        return isset($list[$val]) ? $list[$val] : null;
    }

    /**
     * @param int $month_dev
     * @return false|int|string
     */
    public static function getStudyYearDefault($month_dev = null, $timestamp = null)
    {
        $month_dev = $month_dev == null ? Yii::$app->settings->get('module.study_plan_month_in', 9) : $month_dev;
        $month = $timestamp == null ? date("n") : date("n", $timestamp);
        $year = $timestamp == null ? date("Y") : date("Y", $timestamp);
        return $month < $month_dev ? $year - 1 : $year;
    }

    public static function getStudyYearParams($study_year = null, $month_dev = null)
    {
        $data = [];
        $month_dev = $month_dev == null ? Yii::$app->settings->get('module.study_plan_month_in', 9) : $month_dev;
        $year = $study_year == null ? self::getStudyYearDefault($month_dev) : $study_year;

        $data['timestamp_in'] = mktime(0, 0, 0, $month_dev, 1, $year);
        $data['timestamp_out'] = mktime(0, 0, 0, $month_dev, 1, $year + 1);
        return $data;
    }

    /**
     * @param int $min
     * @param int $max
     * @return array
     */
    public static function getCourseList($min = 1, $max = 9)
    {
        $course = [];
        for ($i = $min; $i <= $max; $i++) {
            $course[$i] = $i . ' класс';
        }
        return $course;
    }

    /**
     * @param int $min
     * @param int $max
     * @return array
     */
    public static function getTermList($min = 1, $max = 8)
    {
        $course = [];
        $per_pr = '';
        for ($i = $min; $i <= $max; $i++) {
            if ($i == 1) $per_pr = " год";
            if ($i == 0 or $i > 4) $per_pr = " лет";
            if ($i > 1 and $i < 5) $per_pr = " года";
            $course[$i] = $i . $per_pr;
        }
        return $course;
    }

    /**
     * @param string $vid
     * @param int $from
     * @param int $to
     * @return array
     */
    public static function getWeekdayList($vid = 'name', $from = 1, $to = 7)
    {
        $weekday_list = ['понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресение'];
        $weekday_list_short = ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'];
        $list = [];
        for ($i = $from; $i <= $to; $i++) {
            $list[$i] = ($vid == 'name') ? $weekday_list[$i - 1] : $weekday_list_short[$i - 1];
        }
        return $list;
    }

    /**
     * @param string $vid
     * @param $val
     * @param int $from
     * @param int $to
     * @return mixed|null
     */
    public static function getWeekdayValue($vid = 'name', $val, $from = 1, $to = 7)
    {
        $weekday_list = self::getWeekdayList($vid, $from, $to);

        return isset($weekday_list[$val]) ? $weekday_list[$val] : null;
    }

    /**
     * @param string $vid
     * @param int $from
     * @param int $to
     * @return array
     */
    public static function getWeekList($vid = 'name', $from = 1, $to = 4)
    {
        $list = [];
        for ($i = $from; $i <= $to; $i++) {
            $list[$i] = ($vid == 'name') ? $i . '-я неделя' : $i . ' нед.';
        }
        return $list;
    }

    /**
     * @param string $vid
     * @param $val
     * @param int $from
     * @param int $to
     * @return mixed|null
     */
    public static function getWeekValue($vid = 'name', $val, $from = 1, $to = 4)
    {
        $week_list = self::getWeekList($vid, $from, $to);

        return isset($week_list[$val]) ? $week_list[$val] : '';
    }

    public static function per($i)
    {
        $per_pr = '';
        if ($i > 20) $i = substr($i, strlen($i) - 1, strlen($i));
        if ($i == 1) $per_pr = "год";
        if ($i == 0 or $i > 4) $per_pr = "лет";
        if ($i > 1 and $i < 5) $per_pr = "года";
        return $per_pr;
    }

//    public static function age($birthday, $timestamp = false)// не точная
//    {
//        $current_time = $timestamp != false ? $timestamp : time();
//        $period = $current_time - $birthday; // возрастной период в секундах
//
//        $age_year = floor($period / (365.2421896 * 24 * 60 * 60)); // полных лет
//        $age_month = floor((round(($period / (365.2421896 * 24 * 60 * 60)), 2) - $age_year) * 12); // полных месяцев за вычетом полных лет
//
//        return [
//            'age_year' => $age_year,
//            'age_month' => $age_month,
//        ];
//    }

// Количество лет, месяцев и дней, прошедших со дня рождения
    /**
     * @param $sec_birthday
     * @param bool $timestamp
     * @return array
     */
    public function age($sec_birthday, $timestamp = false)
    {
        // Сегодняшняя дата или $timestamp
        $sec_now = $timestamp != false ? $timestamp : time();
        // Подсчитываем количество месяцев, лет
        for($time = $sec_birthday, $month = 0;
            $time < $sec_now;
            $time = $time + date('t', $time) * 86400, $month++){
            $rtime = $time;
        }
        $month = $month - 1;
        // Количество лет
        $year = intval($month / 12);
        // Количество месяцев
        $month = $month % 12;
        // Количество дней
        $day = intval(($sec_now - $rtime) / 86400);
        return [
            'age_year' => $year,
            'age_month' => $month,
            'age_day' => $day,
        ];
    }

    public static function getHalfYearList($onlyHalf = false)
    {
        if($onlyHalf) {
        return [
            1 => '1-е полугодие',
            2 => '2-е полугодие',
        ];

        }
        return [
            0 => 'полный год',
            1 => '1-е полугодие',
            2 => '2-е полугодие',
        ];
    }

    public static function getHalfYearValue($val)
    {
        $list = self::getHalfYearList();

        return isset($list[$val]) ? $list[$val] : null;
    }

    public static function getHalfYearParams($study_year = null, $month_dev = null, $half_year = 0)
    {
        $data = [];
        $month_dev = $month_dev == null ? Yii::$app->settings->get('module.study_plan_month_in', 9) : $month_dev;
        $year = $study_year == null ? self::getStudyYearDefault($month_dev) : $study_year;

        if ($half_year == 1) {
            $data['timestamp_in'] = mktime(0, 0, 0, $month_dev, 1, $year);
            $data['timestamp_out'] = mktime(0, 0, 0, 12, 1, $year);
        } elseif ($half_year == 2) {
            $data['timestamp_in'] = mktime(0, 0, 0, 1, 1, $year + 1);
            $data['timestamp_out'] = mktime(0, 0, 0, $month_dev, 1, $year + 1);
        } else {
            $data['timestamp_in'] = mktime(0, 0, 0, $month_dev, 1, $year);
            $data['timestamp_out'] = mktime(0, 0, 0, $month_dev, 1, $year + 1);
        }
        return $data;
    }

    /**
     * Обработчик даты типа 02.2024
     * @param $date
     * @return array
     */
    public static function getMonYearParams($date)
    {
        $date_array = explode('.', $date);
        $mon = $date_array[0];
        $year = $date_array[1];
        $timestamp_in = mktime(0, 0, 0, $mon, 1, $year);
        $day_out = date("t", $timestamp_in);
        $timestamp_out = mktime(23, 59, 59, $mon, $day_out, $year);
        return [$timestamp_in, $timestamp_out];
    }

    /**
     * Обработчик списка дат типа 02.2024,04.2024,03.2024
     * @param string $date
     * @return array
     */
    public static function getMonYearParamsFromList(string $date)
    {
        $timestamp_in = $timestamp_out = [];
        $array = explode(',', $date);
        foreach ($array as $item => $date_item) {
        $date_array = explode('.', $date_item);
        $mon = $date_array[0];
        $year = $date_array[1];
        $t = mktime(0, 0, 0, $mon, 1, $year);
        $day_out = date("t", $t);
        $timestamp_in[] = $t;
        $timestamp_out[] = mktime(23, 59, 59, $mon, $day_out, $year);
        }
        return [min($timestamp_in), max($timestamp_out)];
    }

    /**
     * @param array $dates
     * @return array
     */
    public static function getMonYearParamsFromArray(array $dates)
    {
        $timestamp_in = $timestamp_out = [];
        foreach ($dates as $item => $date_item) {
            $date_array = explode('.', $date_item);
            $mon = $date_array[0];
            $year = $date_array[1];
            $t = mktime(0, 0, 0, $mon, 1, $year);
            $day_out = date("t", $t);
            $timestamp_in[] = $t;
            $timestamp_out[] = mktime(23, 59, 59, $mon, $day_out, $year);
        }
        return [min($timestamp_in), max($timestamp_out)];
    }
    /**
     *  Определяет multiple date
     * @param $date
     * @return bool
     */
    public static function isNonYearList($date)
    {
        $array = explode(',', $date);
        return count($array) > 1;
    }

    /**
     * На выходе массив в формате 2010-02
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws \Exception
     */
    public static function getMonthsInRange($startDate, $endDate, $format = "Y-m")
    {
        $data= [];
        $start    = (new \DateTime($startDate))->modify('first day of this month');
        $end      = (new \DateTime($endDate))->modify('first day of next month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
           $data[] = $dt->format($format);
        }
        return $data;
    }
}