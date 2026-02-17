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
    protected $limit_up_flag; // сообщать о превышении нагрузки в шаблон
    protected $time_min;
    protected $time_max;
    protected $day_time;
    protected $time_limit;
    protected $time_per;
    protected $period_time = 300; // минимальный шаг периода сек.
    protected $error_time = 60;   // погрешность сек.
    protected $teachers_list;
    protected $teachers_fullname;
    protected $teachers_direction;
    protected $teachers_level;
    protected $teachers_tab_num;
    protected $subject_type_flag;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->limit_up_flag = $model_date->limit_up_flag ?? true;
        $this->time_max = Schedule::encodeTime(Yii::$app->settings->get('module.generator_time_max', 21) . ':00'); // Окончание работы (час)
        $this->time_min = Schedule::encodeTime(Yii::$app->settings->get('module.generator_time_min', 8) . ':00'); // Начало работы (час)
        $this->day_time = $this->time_max - $this->time_min; // время в сек, доступное для учебы
        $this->time_limit = Yii::$app->settings->get('module.generator_time_limit', 4) * 60 * 60; // Время работы до перерыва (часов)
        $this->time_per = Yii::$app->settings->get('module.generator_time_per', 30) * 60; // Время перерыва (мин)
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

    /**
     * Формируем расписание
     * @return array
     */
    protected function getTeachersDaySchedule()
    {
        $data_schedule = [];
        foreach ($this->getModelsData() as $teachers_id => $schedule) {
            foreach ($schedule as $week_day => $array) {
                $data_schedule[$teachers_id][$week_day]['schedule'] = $this->generatorSchedule($array);
            }
        }
        return $data_schedule;
    }

    /**
     * Формируем новый массив данных расписания на день, склеивая данные по порядку и по типу обучения
     * @param $array
     * @param $factor
     * @return array
     */
    protected function mergeMaster($array)
    {
        array_multisort(array_column($array, 'time_in'), SORT_ASC, $array);
        // echo '<pre>' . print_r($array, true) . '</pre>'; die();
        $const = 0;
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
        }
        return $d;
    }

    /**
     * Нормализация массива
     * @param $d
     * @return array
     */
    protected function normaliseArray($d)
    {
        $array = [];
        foreach ($d as $item => $data) {
            if ($this->subject_type_flag == 1 && $data['subject_type_id'] == 1001) { // Не выводим внебюджет при флаге = бюджет
                continue;
            }
            if ($this->subject_type_flag == 2 && $data['subject_type_id'] == 1000) { // Не выводим бюджет при флаге = внебюджет
                continue;
            }
            $array[$item]['time_in'] = ceil($data['time_in'] / $this->period_time) * $this->period_time;   // Нормализуем к 5-мин периоду
            $array[$item]['time_out'] = ceil($data['time_out'] / $this->period_time) * $this->period_time; // Нормализуем к 5-мин периоду
            $array[$item]['subject_type_id'] = $data['subject_type_id'];

            $string = Schedule::decodeTime($array[$item]['time_in']) . '-' . Schedule::decodeTime($array[$item]['time_out']);

            $string .= $data['subject_type_id'] == 0 ? ' - перерыв' : '';

            if ($this->subject_type_flag != 1) {
                $string .= $data['subject_type_id'] == 1001 && $this->subject_type_flag != 2 ? ' - в/б' : '';
            }

            $array[$item][$data['subject_type_id'] != 0 ? 'time_disp' : 'time_per_disp'] = $string;
        }
//        echo '<pre>' . print_r($array, true) . '</pre>';
        return $array;
    }

    /**
     * Промежутки между занятиями
     * @param $array
     * @return array
     */
    protected function getScheduleFreeItem($array)
    {
        $data = [];
        for ($i = 0; $i <= count($array); $i++) {
            if ($i == 0) {
                $subject_type_up = 0;
                $time_in = $this->time_min;
                $time_out = $array[$i]['time_in'];
                $subject_type_down = $array[$i]['subject_type_id'];
                $priority = 2;
            } elseif ($i == count($array)) {
                $subject_type_up = $array[$i - 1]['subject_type_id'];
                $time_in = $array[$i - 1]['time_out'];
                $time_out = $this->time_max;
                $subject_type_down = 0;
                $priority = 3;
            } else {
                $subject_type_up = $array[$i - 1]['subject_type_id'];
                $time_in = $array[$i - 1]['time_out'];
                $time_out = $array[$i]['time_in'];
                $subject_type_down = $array[$i]['subject_type_id'];
                $priority = 1;
            }
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

        return $data;
    }

    /**
     * Анализ расписания
     * @param $array
     * @return array
     */
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
        arsort($auditory_time); // отсортируем по меньшему времени
        $auditory_up_time = $auditory_time_summ / 3; // время превышения при пересчете академ на астрономич аудиторного времени
        $count_per = ($auditory_time_summ + $auditory_up_time) >= $this->time_limit ? 1 : 0;  // если больше 4-х часов, то 1 перерыв. Иначе - без перерыва
        $auditory_reserv_time = $this->day_time - $auditory_time_summ - $auditory_up_time - ($this->time_per * $count_per); // оставшееся аудиторное время за вычетом перерывов

        return $d = [
            'min_time' => $time_in,
            'max_time' => $time_out,
            'auditory_time' => $auditory_time, // array
            'auditory_up_time' => $auditory_up_time,
            'pause_needs' => $count_per,
            'auditory_reserv_time' => $auditory_reserv_time,
            'error_flag' => ($auditory_reserv_time + $this->error_time) < 0 ? true : false,
        ];
    }

    /**
     * Генерация графика работы
     * @param $arrayMaster
     * @return array
     */
    protected function generatorSchedule($arrayMaster)
    {
        $analiticArray = $this->getScheduleAnalitic($arrayMaster);
        $freeArray = $this->getScheduleFreeItem($arrayMaster);

        if ($analiticArray['error_flag'] == true && $this->limit_up_flag) {
            $arrayMaster[] = [
                'time_disp' => 'Превышение нагрузки на ' . round(abs($analiticArray['auditory_reserv_time']) / 3600, 2) . ' ч.',
            ];
            return $arrayMaster;
        }
        if ($analiticArray['pause_needs'] == 1) {
            array_multisort(array_column($freeArray, 'priority'), SORT_ASC, $freeArray);
            $this->setLunchBreak($freeArray, $arrayMaster);
        }
        $this->setUpTime($freeArray, $arrayMaster, $analiticArray);
        $d = $this->mergeMaster($arrayMaster);

        //  echo '<pre>' . print_r($freeArray, true) . '</pre>';
        return $this->normaliseArray($d); // нормализация
    }

    /**
     * Обеденный перерыв - установка
     * @param $array
     * @param $arrayMaster
     */
    protected function setLunchBreak(&$array, &$arrayMaster)
    {
        foreach ($array as $i => $item) {
            if ($item['time'] < $this->time_per) {
                continue;
            }
            if ($item['subject_type_up'] == 0) { // Притягиваем снизу
                $time_in = $item['time_out'] - $this->time_per;
                $time_out = $item['time_out'];
                $free_update[] = [
                    'subject_type_up' => $item['subject_type_up'],
                    'time_in' => $item['time_in'],
                    'time_out' => $time_in,
                    'time' => $time_in - $item['time_in'],
                    'subject_type_down' => $item['subject_type_down'],
                    'priority' => $item['priority'],
                ];
                $lunch_break = [
                    'subject_type_id' => 0, // обозначаем перерыв - индекс = 0
                    'time_in' => $time_in,
                    'time_out' => $time_out,
                ];
            } else { // Притягиваем сверху
                $time_in = $item['time_in'];
                $time_out = $item['time_in'] + $this->time_per;
                $free_update[] = [
                    'subject_type_up' => $item['subject_type_up'],
                    'time_in' => $time_out,
                    'time_out' => $item['time_out'],
                    'time' => $item['time_out'] - $time_out,
                    'subject_type_down' => $item['subject_type_down'],
                    'priority' => $item['priority'],
                ];
                $lunch_break = [
                    'subject_type_id' => 0, // обозначаем перерыв - индекс = 0
                    'time_in' => $time_in,
                    'time_out' => $time_out,
                ];
            }

            if ($time_in > $item['time_in']) {
                array_splice($array, $i, 1, $free_update);
            } else {
                array_splice($array, $i, 1);
            }
            array_unshift($arrayMaster, $lunch_break);
            break;
        }

    }

    /**
     * Установка дополнительного астронамического времени по приоритету
     * @param $freeArray
     * @param $arrayMaster
     * @param $analiticArray
     */
    protected function setUpTime(&$freeArray, &$arrayMaster, $analiticArray)
    {
        foreach ($analiticArray['auditory_time'] as $subject_type_id => $time) {
            array_multisort(array_column($freeArray, 'priority'), SORT_ASC, $freeArray);
            $t = ceil($time / 3);
            $clone = $freeArray;
            foreach ($clone as $i => $item) {
                if ($t <= 0) {
                    break;
                }
                if ($item['subject_type_up'] == 0) { // Притягиваем снизу
                    $up_time = [
                        'subject_type_id' => $subject_type_id,
                        'time_in' => $t < $item['time'] ? $item['time_out'] - $t : $item['time_in'],
                        'time_out' => $item['time_out'],
                    ];
                    $update = [
                        'subject_type_up' => $item['subject_type_up'],
                        'time_in' => $item['time_in'],
                        'time_out' => $t < $item['time'] ? $item['time_out'] - $t : $item['time_in'],
                        'time' => $item['time'] - $t,
                        'subject_type_down' => $item['subject_type_down'],
                        'priority' => $item['priority'],
                    ];
                } else { // Притягиваем сверху
                    $up_time = [
                        'subject_type_id' => $subject_type_id,
                        'time_in' => $item['time_in'],
                        'time_out' => $t < $item['time'] ? $item['time_in'] + $t : $item['time_out'],
                    ];
                    $update = [
                        'subject_type_up' => $item['subject_type_up'],
                        'time_in' => $t < $item['time'] ? $item['time_in'] + $t : $item['time_out'],
                        'time_out' => $item['time_out'],
                        'time' => $item['time'] - $t,
                        'subject_type_down' => $item['subject_type_down'],
                        'priority' => $item['priority'],
                    ];
                }
                if ($t < $item['time']) {
                    array_push($freeArray, $update);
                    unset($freeArray[$i]);
                } else {
                    unset($freeArray[$i]);
                }
                array_push($arrayMaster, $up_time);

                $t = $t < $item['time'] ? 0 : $t - $item['time'];
            }
        }
    }

    /**
     * формирование отчета
     * @throws \yii\base\InvalidConfigException
     */
    public function makeXlsx()
    {
        $data[] = [
            'rank' => 'doc',
            'type' => $this->subject_type_flag == 1 ? 'бюджет' : ($this->subject_type_flag == 2 ? 'внебюджет' : 'бюджет и внебюджет'),
            'plan_year' => ArtHelper::getStudyYearsValue($this->plan_year),
        ];
        $items = [];
        $i = 0;
        $dada_sch = [];
        foreach ($this->getTeachersDaySchedule() as $teachers_id => $value) {
            foreach ($value as $day => $d) {
                foreach ($d['schedule'] as $item => $dd) {
                    isset($dd['time_disp']) ? $dada_sch[$teachers_id][$day][] = $dd['time_disp'] : null;
                    isset($dd['time_per_disp']) && count($d['schedule']) > 1 ? $dada_sch[$teachers_id][$day][] = $dd['time_per_disp'] : null;
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

    public static function getTypeList()
    {
        return [
            1 => 'только Бюджет',
            2 => 'только Внебюджет',
            3 => 'Бюджет и Внебюджет',
        ];
    }
}

