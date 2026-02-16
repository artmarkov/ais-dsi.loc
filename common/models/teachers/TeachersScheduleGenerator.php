<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\Schedule;
use common\models\user\UserCommon;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class TeachersScheduleGenerator
{
    const template_timesheet = 'document/generator_schedule.xlsx';

    protected $plan_year;
    protected $limit_up_flag = true; // сообщать о превышении нагрузки в шаблон
    protected $time_min = '8:00';
    protected $time_max;
    protected $day_time; // время в сек, доступное для учебы
    protected $time_limit;
    protected $time_per;
    protected $period_time = 300; // минимальный шаг периода сек.
    protected $teachers_list;
    protected $teachers_fullname;
    protected $teachers_direction;
    protected $teachers_level;
    protected $teachers_tab_num;
    protected $subject_type_flag;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->time_max = Schedule::encodeTime(Yii::$app->settings->get('module.generator_time_max', 21) . ':00');
        $this->time_min = Schedule::encodeTime($this->time_min);
        $this->day_time = $this->time_max - $this->time_min;
        $this->time_limit = Yii::$app->settings->get('module.generator_time_limit', 4) * 60 * 60;
        $this->time_per = Yii::$app->settings->get('module.generator_time_per', 30) * 60;
        $this->teachers_list = $model_date->teachers_list;
        $this->subject_type_flag = $model_date->subject_type_flag;
        $this->getTeacherInfo();
//        echo '<pre>' . print_r($this->time_limit, true) . '</pre>';
    }

    protected function getModelsData()
    {
        $models = (new Query())->from('subject_schedule_view')
            ->select('teachers_id, subject_type_id, week_day, time_in, time_out')
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['status' => 1])
            ->orderBy('teachers_id, week_day, time_in')
            ->all();
        return ArrayHelper::index($models, null, ['teachers_id', 'week_day']);
    }

    protected function getTeacherInfo()
    {
        $models = TeachersActivityView::find()
            ->where(['user_common_status' => UserCommon::STATUS_ACTIVE])
            ->andWhere(['in', 'teachers_id', $this->teachers_list])
            ->orderBy('last_name, first_name, middle_name, direction_id, direction_vid_id')
            ->asArray()
            ->all();
        $m = ArrayHelper::index($models, 'teachers_id');
        $this->teachers_fullname = ArrayHelper::getColumn($m, 'fullname');
        $this->teachers_tab_num = ArrayHelper::getColumn($m, 'tab_num');
        foreach (ArrayHelper::index($models, null, ['teachers_id']) as $teachers_id => $teachersInfo) {
            $teachers_direction = [];
            $teachers_level = [];
            foreach ($teachersInfo as $item => $value) {
                if (isset($value['direction_id'])) {
                    $teachers_direction[$value['direction_id']] = $value['direction_slug'];
                    $teachers_level[$value['direction_id']] = $value['level_slug'];
                }
            }
            $this->teachers_direction[$teachers_id] = implode('/', $teachers_direction);
            $this->teachers_level[$teachers_id] = implode('/', $teachers_level);
        }
//        echo '<pre>' . print_r($this->teachers_level, true) . '</pre>';
    }

    protected function getTeachersDaySchedule()
    {
        $data_schedule = [];

//        $models = (new Query())->from('subject_schedule_view')
//            ->select('teachers_id, subject_type_id, week_day, time_in, time_out')
//            ->where(['in', 'teachers_id', $this->teachers_list])
//            ->andWhere(['plan_year' => $this->plan_year])
//            // ->andWhere(['subject_type_id' => $this->subject_type_id])
//            ->andWhere(['status' => 1])
//            ->orderBy('teachers_id, week_day, time_in')
//            // ->groupBy('teachers_id, week_day, time_out, time_in')
//            ->all();
//        $models = ArrayHelper::index($models, null, ['teachers_id', 'week_day']);
//        echo '<pre>' . print_r($models, true) . '</pre>';
        foreach ($this->getModelsData() as $teachers_id => $schedule) {
            foreach ($schedule as $week_day => $array) {
                // echo '<pre>' . print_r($array, true) . '</pre>';
                $data_schedule[$teachers_id][$week_day]['schedule'] = $this->generatorSchedule($array);
            }
        }
//        foreach ($models as $item => $data) {
//            if (isset($data['week_day'])) {
//                $time = Schedule::astr2academ($data['time']) * 3600; // приравниваем академический час к астрономическому
//                if ($time >= $this->time_limit) { // если больше 4-х часов, то 1 перерыв. Иначе - без перерыва
//                    $count_per = 1;
//                    $time_limit = $time / 2;
//                    $time_limit = ceil($time_limit / $this->period_time) * $this->period_time; // выравниваем по 5 - мин интервалу
//                } else {
//                    $count_per = 0;
//                    $time_limit = $time;
//                }
//                $full_time = $time + ($count_per * $this->time_per);
//
//                $time_out_max = $data['min_time'] + $full_time; // расчитываем максимальное время окончания с учетом перерывов
//                $time_out_max = ceil($time_out_max / $this->period_time) * $this->period_time; // выравниваем по 5 - мин интервалу
//
//                $min_time = $data['min_time'];
//                if ($time_out_max > $this->time_max) {
//                    $n = ceil(($time_out_max - $this->time_max) / $this->period_time); // выравниваем по 5 - мин интервалу
//                    $min_time = $min_time - ($n * $this->period_time);
//                    $time_out_max = $this->time_max;
//                }
//                $data_schedule[$data['teachers_id']][$data['week_day']]['time'] = $time;
//                $data_schedule[$data['teachers_id']][$data['week_day']]['min_time'] = $min_time;
//                $data_schedule[$data['teachers_id']][$data['week_day']]['max_time'] = $time_out_max;
//                $data_schedule[$data['teachers_id']][$data['week_day']]['count_per'] = $count_per;
//
//                $d = [];
//                $per = 0;
//                for ($i = 1; $i <= $count_per + 1; $i++) {
//                    if ($this->limit_up_flag && ($full_time > ($this->time_max - $this->time_min))) {
//                        $d[$i]['time_disp'] = 'Превышение нагрузки на ' . round(($full_time - ($this->time_max - $this->time_min)) / 3600, 1) . ' ч.';
//                        break;
//                    }
//                    if ($time_out_max == $min_time) break;
//                    $time_out = $min_time + $time_limit;
//                    if ($time_out > $time_out_max) $time_out = $time_out_max;
//                    $d[$i]['time'] = [$min_time, $time_out];
//                    if ($i == $count_per + 1) {
//                        $time_out = $time_out < $data['max_time'] ? $data['max_time'] : $time_out_max;
//                    }
//                    $d[$i]['time_disp'] = Schedule::decodeTime($min_time) . '-' . Schedule::decodeTime($time_out);
//                    $time_per_out = $time_out + $this->time_per;
//                    if ($time_per_out > $time_out_max) {
//                        break;
//                    }
//                    if ($per == 0) {
//                        $d[$i]['time_per'] = [$time_out, $time_per_out, $time_out_max];
//                        $d[$i]['time_per_disp'] = Schedule::decodeTime($time_out) . '-' . Schedule::decodeTime($time_per_out);
//                        $min_time = $time_per_out;
//                        $per = 1;
//                    }
//                }
//                $data_schedule[$data['teachers_id']][$data['week_day']]['schedule'] = $d;
//
//            }
//        }
//        echo '<pre>' . print_r($data_schedule, true) . '</pre>';
        return $data_schedule;
    }

    /**
     * Формируем новый массив данных расписания на день, группируя данные по порядку и по типу обучения
     * @param $array
     * @param $factor
     * @return array
     */
    protected function mergeMaster($array)
    {
        array_multisort(array_column($array, 'time_in'), SORT_ASC, $array);
        // echo '<pre>' . print_r($array, true) . '</pre>'; die();
        $const = 10;
        $i = 0;
        $d = [];
        foreach ($array as $item => $data) {
            if ($item == 0) {
                $d[$i]['subject_type_id'] = $data['subject_type_id'];
                $d[$i]['time_in'] = $data['time_in'];
                $d[$i]['time_out'] = $data['time_out'];
            } else {
                if ($data['subject_type_id'] == $const) {
                    $d[$i]['time_out'] = $d[$i]['time_out'] + ($data['time_out'] - $data['time_in']);
                } else {
                    $i = $i + 1;
                    $d[$i]['subject_type_id'] = $data['subject_type_id'];
                    $d[$i]['time_in'] = $d[$i - 1]['time_out'];
                    $d[$i]['time_out'] = $data['time_out'];
                }
            }
            $const = $data['subject_type_id'];
            $d[$i][$const != 0 ? 'time_disp' : 'time_per_disp'] = Schedule::decodeTime($d[$i]['time_in']) . '-' . Schedule::decodeTime($d[$i]['time_out']);
        }
        return $d;
    }

    protected function getScheduleFreeItem($array)
    {
        $data = [];
        for ($i = 0; $i <= count($array); $i++) {
            if ($i == 0) {
                $subject_type_up = 0;
                $time_in = $this->time_min;
                $time_out = $array[$i]['time_in'];
                $subject_type_down = $array[$i]['subject_type_id'];
                $priority = [
                    1000 => $subject_type_down == 1000 ? 2 : 4,
                    1001 => $subject_type_down == 1001 ? 2 : 4
                ];
            } elseif ($i == count($array)) {
                $subject_type_up = $array[$i - 1]['subject_type_id'];
                $time_in = $array[$i - 1]['time_out'];
                $time_out = $this->time_max;
                $subject_type_down = 0;
                $priority = [
                    1000 => $subject_type_up == 1000 ? 3 : 4,
                    1001 => $subject_type_up == 1001 ? 3 : 4
                ];
            } else {
                $subject_type_up = $array[$i - 1]['subject_type_id'];
                $time_in = $array[$i - 1]['time_out'];
                $time_out = $array[$i]['time_in'];
                $subject_type_down = $array[$i]['subject_type_id'];
                $priority = [
                    1000 => $subject_type_up == 1000 || $subject_type_down == 1000 ? 1 : 4,
                    1001 => $subject_type_up == 1001 || $subject_type_down == 1001 ? 1 : 4
                ];
            }
            /*$priority = 0;
            if ($subject_type_up == 1000) {
                if ($subject_type_down == 0) {
                    $priority = 6;
                } elseif ($subject_type_down == 1000) {
                    $priority = 1;
                } elseif ($subject_type_down == 1001) {
                    $priority = 2;
                }
            } elseif ($subject_type_up == 1001) {
                if ($subject_type_down == 0) {
                    $priority = 7;
                } elseif ($subject_type_down == 1001) {
                    $priority = 3;
                } elseif ($subject_type_down == 1000) {
                    $priority = 2;
                }
            } elseif ($subject_type_up == 0) {
                if ($subject_type_down == 1001) {
                    $priority = 5;
                } elseif ($subject_type_down == 1000) {
                    $priority = 4;
                }
            }*/
            if ($time_in != $time_out) {
                $data[] = [
                    'subject_type_up' => $subject_type_up,
                    'time_in' => $time_in,
                    'time_out' => $time_out,
                    'time' => $time_out - $time_in,
                    'subject_type_down' => $subject_type_down,
                    'priority' => $priority,
                ];
            }
        }
        array_multisort(array_column($data, 'priority'), SORT_ASC, $data);

        return $data;
    }

    protected function getScheduleAnalitic($array)
    {
        $time_in = min(array_column($array, 'time_in'));
        $time_out = max(array_column($array, 'time_out'));
        $auditory_time = [1000 => 0, 1001 => 0];
        $auditory_time_summ = 0;
        // сумма аудиторных академ часов
        foreach ($array as $i => $item) {
            $auditory_time[$item['subject_type_id']] += ($item['time_out'] - $item['time_in']);
            $auditory_time_summ += ($item['time_out'] - $item['time_in']);
        }
        asort($auditory_time); // отсортируем по меньшему времени
        // echo '<pre>' . print_r($auditory_time, true) . '</pre>'; die();
        $auditory_up_time = $auditory_time_summ / 3; // время превышения при пересчете академ на астрономич аудиторного времени
        $count_per = ($auditory_time_summ + $auditory_up_time) >= $this->time_limit ? 1 : 0;  // если больше 4-х часов, то 1 перерыв. Иначе - без перерыва
        $auditory_reserv_time = $this->day_time - $auditory_time_summ - $auditory_up_time - ($this->time_per * $count_per); // оставшееся аудиторное время за вычетом перерывов

        $error_flag = false;
        if ($auditory_reserv_time < 0) {
            $error_flag = true;
        }

        $d = [
            'min_time' => $time_in,
            'max_time' => $time_out,
            'auditory_time' => $auditory_time,
            'auditory_up_time' => $auditory_up_time,
            'pause_needs' => $count_per,
            'auditory_reserv_time' => $auditory_reserv_time,
            'error_flag' => $error_flag,
        ];

        return $d;
    }

    protected function generatorSchedule($arrayMaster)
    {
        $analiticArray = $this->getScheduleAnalitic($arrayMaster);
        $freeArray = $this->getScheduleFreeItem($arrayMaster);

        if ($analiticArray['error_flag'] == true) {
            $arrayMaster[] = [
                'time_disp' => 'Превышение нагрузки на ' . round(abs($analiticArray['auditory_reserv_time']) / 3600, 1) . ' ч.',
            ];
            return $arrayMaster;
        }
        if ($analiticArray['pause_needs'] == 1) {
            $this->setLunchBreak($freeArray, $arrayMaster);
        }
      // echo '<pre>' . print_r($analiticArray, true) . '</pre>';die();
//        echo '<pre>' . print_r($freeArray, true) . '</pre>';
        $this->setUpTime($freeArray, $arrayMaster, $analiticArray);
//         echo '<pre>' . print_r($arrayMaster, true) . '</pre>'; die();


        return $this->mergeMaster($arrayMaster);
    }

    protected function setLunchBreak(&$array, &$arrayMaster)
    {
//        echo '<pre>' . print_r($array, true) . '</pre>'; die();
        foreach ($array as $i => $item) {
            if ($item['time'] < $this->time_per) {
                continue;
            }
            $time_in = $item['time_out'] - $this->time_per;
            $time_out = $item['time_out'];

            $free_update[] = [
                'subject_type_up' => $item['subject_type_up'],
                'time_in' => $item['time_in'],
                'time_out' => $time_in,
                'time' => $time_in - $item['time_in'],
                'subject_type_down' => $item['subject_type_down'],
                'priority' => $item['priority'], // дозаписать в первую очередь
            ];
            $lunch_break = [
                'subject_type_id' => 0, // обозначаем перерыв - индекс = 0
                'time_in' => $time_in,
                'time_out' => $time_out,
            ];
            if ($time_in > $item['time_in']) {
                array_splice($array, $i, $i, $free_update);
            } else {
                array_splice($array, $i, $i);
            }
            array_unshift($arrayMaster, $lunch_break);
            break;
        }

    }

    protected function setUpTime(&$freeArray, &$arrayMaster, $analiticArray)
    {


        foreach ($analiticArray['auditory_time'] as $subject_type_id => $time) {
        array_multisort(array_column($freeArray['priority'][$subject_type_id], $subject_type_id), SORT_ASC, array_column($freeArray, 'time'), SORT_ASC, $freeArray);
        echo '<pre>' . print_r($freeArray, true) . '</pre>';
            $t = ceil($time / 3);
            foreach ($freeArray as $i => $item) {
                if ($t == 0) {
                    break;
                }
                if ($item['subject_type_up'] == 0) {
                    $up_time = [
                        'subject_type_id' => $subject_type_id,
                        'time_in' => $t < $item['time'] ? $item['time_out'] - $t : $item['time_in'],
                        'time_out' => $item['time_out'],
                    ];
                    $update[] = [
                        'subject_type_up' => $item['subject_type_up'],
                        'time_in' => $item['time_in'],
                        'time_out' => $t < $item['time'] ? $item['time_out'] - $t : $item['time_in'],
                        'time' => $item['time'] - $t,
                        'subject_type_down' => $item['subject_type_down'],
                        'priority' => $item['priority'],
                    ];
                } else {
                    $up_time = [
                        'subject_type_id' => $subject_type_id,
                        'time_in' => $item['time_in'],
                        'time_out' => $t < $item['time'] ? $item['time_in'] + $t : $item['time_out'],
                    ];
                    $update[] = [
                        'subject_type_up' => $item['subject_type_up'],
                        'time_in' => $t < $item['time'] ? $item['time_in'] + $t : $item['time_out'],
                        'time_out' => $item['time_out'],
                        'time' => $item['time'] - $t,
                        'subject_type_down' => $item['subject_type_down'],
                        'priority' => $item['priority'],
                    ];
                }

                if ($t < $item['time']) {

                    array_splice($freeArray, $i, $i, $update);
                } else {
                    array_splice($freeArray, $i, $i);
                }
                array_unshift($arrayMaster, $up_time);

                $t = $t < $item['time'] ? 0 : $t - $item['time'];
            }
            break;
        }

    }


    public static function getTypeList()
    {
        return [
            1 => 'только Бюджет',
            2 => 'только Внебюджет',
            3 => 'Бюджет и Внебюджет',
        ];
    }

    /**
     * @param $time_in
     * @param $time_out
     * @param float $factor
     * @return float|int
     */
    protected function getPeriod($time_in, $time_out, $factor = false)
    {
        $factor = $factor == true ? 0.75 : 1;
        $time = ($time_out - $time_in) / $factor; // Академическое время переводим в астрономическое
        // $time = ceil($time / $this->period_time) * $this->period_time; // выравниваем по 5 - мин интервалу
        return $time;
    }

    /**
     * формирование расписания
     * @throws \yii\base\InvalidConfigException
     */
    public function makeXlsx()
    {
        $data[] = [
            'rank' => 'doc',
            'type' => $this->subject_type_flag == 1 ? 'бюджет' : ($this->subject_type_flag == 2 ? 'внебюджет' : 'бюджет и внебюджет'),
            'plan_year' => ArtHelper::getStudyYearsValue($this->plan_year),
        ];
//        echo '<pre>' . print_r($attributes, true) . '</pre>'; die();
        $items = [];
        $i = 0;
        $dada_sch = [];
        foreach ($this->getTeachersDaySchedule() as $teachers_id => $value) {
            foreach ($value as $day => $d) {
                foreach ($d['schedule'] as $item => $dd) {
                    isset($dd['time_disp']) ? $dada_sch[$teachers_id][$day][] = $dd['time_disp'] : null;
                    isset($dd['time_per_disp']) ? $dada_sch[$teachers_id][$day][] = $dd['time_per_disp'] . '- пер.' : null;
                }
            }
        }
        foreach ($this->teachers_fullname as $teachers_id => $fullname) {
            $day = [];
            for ($k = 1; $k <= 6; $k++) {
                $day['day_' . $k] = implode("\n", $dada_sch[$teachers_id][$k] ?? []);
            }
            $items[] = array_merge([
                'rank' => 'a',
                'item' => $i + 1,
                'fullname' => $fullname,
                'level_slug' => $this->teachers_level[$teachers_id],
                'direction_slug' => $this->teachers_direction[$teachers_id],
                'tab_num' => $this->teachers_tab_num[$teachers_id],
            ], $day);
            $i++;
        }
//        echo '<pre>' . print_r($items, true) . '</pre>';
//        die();
        $output_file_name = str_replace('.', '_' . Yii::$app->formatter->asDate(time(), 'php:Y_m_d H_i') . '.', basename(self::template_timesheet));

        $tbs = DocTemplate::get(self::template_timesheet)->setHandler(function ($tbs) use ($data, $items) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('a', $items);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }
}

