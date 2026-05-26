<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\Schedule;
use artsoft\helpers\StringHelper;
use common\models\guidejob\Direction;
use common\models\guidejob\DirectionVid;
use common\models\studyplan\StudyplanSubjectHist;
use common\models\subject\SubjectType;
use common\models\subject\SubjectVid;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class TeachersScheduleGenerator
{
    const template_timesheet = 'document/generator_schedule.xlsx';

    protected $keys;
    protected $key_name;
    protected $plan_year;
    protected $activities;
    protected $activitiesArray;
    protected $activity_list;
    protected $teachers_list;
    protected $subject_type_id;
    protected $limit_up_flag; // сообщать о превышении нагрузки в шаблон
    protected $is_normalise_period = true; // Приводить даты к $period_time -мин периоду
    protected $period_time = 300; // минимальный шаг периода сек.
    protected $error_time = 60;   // погрешность при определении перерыва сек.
    protected $time_min; // Начало работы школы(час)
    protected $time_max; // Окончание работы школы(час)
    protected $day_time; // Время в сек, доступное для учебы в школе
    protected $time_limit = [];  // Время работы до перерыва (часов) для бюджета и внебюджета
    protected $time_per;  // Время перерыва (мин)

    protected $teachers_fullname;
    protected $teachers_direction;
    protected $teachers_level;
    protected $teachers_tab_num;

    public function __construct($model_date)
    {
        $this->initKey();
        $this->activity_list = $model_date->activity_list;
        $this->activities = $this->getTeachersActivities();
        $this->activitiesArray = $this->getActivitiesArray();
        $this->teachers_list = $this->getTeachersList();
        $this->plan_year = $model_date->plan_year;
        $this->subject_type_id = $model_date->subject_type_id;
        $this->limit_up_flag = $model_date->limit_up_flag ?? true;
        $this->time_min = Schedule::encodeTime(Yii::$app->settings->get('module.generator_time_min', 8) . ':00');
        $this->time_max = Schedule::encodeTime(Yii::$app->settings->get('module.generator_time_max', 21) . ':00');
        $this->day_time = $this->time_max - $this->time_min;
        $this->time_limit = [
            1000 => Yii::$app->settings->get('module.generator_time_limit', 4) * 60 * 60,
            1001 => Yii::$app->settings->get('module.generator_time_limit_academ', 5) * 2700
        ];
        $this->time_per = Yii::$app->settings->get('module.generator_time_per', 30) * 60;
        $this->getTeacherInfo();
//        echo '<pre>' . print_r($this->time_limit, true) . '</pre>';
    }

    protected function getTeachersActivities()
    {
        $models = TeachersActivityView::find()
            ->where(['in', 'teachers_activity_id', $this->activity_list])
            ->orderBy('last_name, first_name, middle_name, direction_id, direction_vid_id')
            ->asArray()
            ->all();
        return $models;
    }

    protected function getActivitiesArray()
    {
        return ArrayHelper::index($this->activities, null,['teachers_id', 'direction_id', 'direction_vid_id']);
    }

    protected function getTeachersList()
    {
        $teachers_list = ArrayHelper::getColumn($this->activities, 'teachers_id');
        return array_unique($teachers_list);
    }

    protected function getModelsData()
    {
        $models = (new Query())->from('subject_schedule_view')
            ->select(new \yii\db\Expression('teachers_id, subject_type_id, direction_id, direction_vid_id,
            concat(subject_type_id, \'|\', direction_id, \'|\', direction_vid_id) AS key,
             week_day, time_in, time_out'))
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['not', ['subject_schedule_id' => null]])
            ->andWhere(['=', 'plan_year', $this->plan_year])
            ->andWhere(['OR',
                ['status' => 1],
                ['AND',
                    ['status' => 0],
                    ['status_reason' => [1, 2, 4]]
                ],
            ])
            ->andWhere(['not in', 'studyplan_subject_id', StudyplanSubjectHist::getStudyplanSubjectPass() ?? []])
            ->orderBy('teachers_id, week_day, time_in')
            ->all();
        return ArrayHelper::index($models, null, ['teachers_id', 'week_day']);
    }

    /**
     * Получение информации о преподавателях
     */
    protected function getTeacherInfo()
    {
        $models = TeachersActivityView::find()
            ->where(['teachers_id' => $this->teachers_list])
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
//                    $teachers_level[$value['direction_id']] = $value['level_slug'];
                }
            }
            $this->teachers_direction[$teachers_id] = implode('/', $teachers_direction);
            $this->teachers_level[$teachers_id] = implode('/', $teachers_level);
        }
    }

    /**
     * Формирование графика работы
     * @return array
     */
    protected function getTeachersDaySchedule()
    {
        $data_schedule = [];
        foreach ($this->getModelsData() as $teachers_id => $schedule) {
            foreach ($schedule as $week_day => $array) {
                $data_schedule[$teachers_id][$week_day]['schedule'] = $this->generatorWorkSchedule($teachers_id, $array);
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
        //  echo '<pre>' . print_r($array, true) . '</pre>'; die();
        $const = 1;
        $i = 0;
        $d = [];
        foreach ($array as $item => $data) {
            if ($item == 0) {
                $d[$i]['key'] = $data['key'];
                $d[$i]['time_in'] = $data['time_in'];
                $d[$i]['time_out'] = $data['time_out'];
            } else {
                if ($data['key'] == $const && $d[$i]['time_out'] == $data['time_in']) {
                    $d[$i]['time_out'] = $d[$i]['time_out'] + ($data['time_out'] - $data['time_in']);
                } else {
                    $i = $i + 1;
                    $d[$i]['key'] = $data['key'];
                    $d[$i]['time_in'] = $data['time_in'];
                    $d[$i]['time_out'] = $data['time_out'];
                }
            }
            $const = $data['key'];
        }
        return $d;
    }

    /**
     * Нормализация массива
     * @param $d
     * @param $analiticArray
     * @return array
     */
    protected function normaliseArray($teachers_id, $d, $analiticArray)
    {
        $array = [];
        foreach ($d as $item => $data) {
            if($data['key'] != 0) {
                $key = explode('|', $data['key']);
                if (!isset($this->activitiesArray[$teachers_id][$key[1]][$key[2]])) {
                    continue;
                }
            }
            if ($this->subject_type_id == 1000 && $this->getType($data['key']) == 1001) { // Не выводим внебюджет при флаге = бюджет
                continue;
            }
            if ($this->subject_type_id == 1001 && $this->getType($data['key']) == 1000) { // Не выводим бюджет при флаге = внебюджет
                continue;
            }
            if ($this->subject_type_id == 1000 && $data['key'] == 0 && $analiticArray['pause_needs'][1000] == 0) { // Не выводим перерыв
                continue;
            }
            if ($this->subject_type_id == 1001 && $data['key'] == 0 && $analiticArray['pause_needs'][1001] == 0) { // Не выводим перерыв
                continue;
            }
            // Нормализуем к 5-мин периоду
            $array[$item]['time_in'] = $this->normalisePeriod($data['time_in']);
            $array[$item]['time_out'] = $this->normalisePeriod($data['time_out']);
            $array[$item]['key'] = $data['key'];

            $string = Schedule::decodeTime($array[$item]['time_in']) . '-' . Schedule::decodeTime($array[$item]['time_out']);

            $string .= $data['key'] == 0 ? ' - перерыв' : $this->key_name[$data['key']];

            $array[$item][$data['key'] != 0 ? 'time_disp' : 'time_per_disp'] = $string;
        }
        return $array;
    }

    /**
     * Нормализация к 5-мин периоду
     * @param $time
     * @param $method
     * @return float|int
     */
    protected function normalisePeriod($time, $method = 'down')
    {
        return $this->is_normalise_period ? ($method == 'down' ? ceil($time / $this->period_time) : floor($time / $this->period_time)) * $this->period_time : $time;
    }

    /**
     * Промежутки между занятиями
     * $key_up и $key_down - переменные для притягивания уроков по типу сверху или снизу
     * @param $array
     * @param $analiticArray
     * @return array
     */
    protected function getScheduleFreeItem($array)
    {
        $data = [];
        for ($i = 0; $i < count($array) + 1; $i++) {
            if ($i == 0) { // берем первый промежуток от начала работы школы до первого занятия
                $key_up = 0;
                $time_in = $this->time_min;
                $time_out = $array[$i]['time_in'];
                $key_down = $array[$i]['key'];
                $priority = 100000;
            } elseif ($i == count($array)) { // берем последний промежуток от конца последнего занятия до конца работы школы
                $key_up = $array[$i - 1]['key'];
                $time_in = $array[$i - 1]['time_out'];
                $time_out = $this->time_max;
                $key_down = 0;
                $priority = 100100;
            } else { // вычисляем промежутки между занятиями
                $key_up = $array[$i - 1]['key'];
                $time_in = $array[$i - 1]['time_out'];
                $time_out = $array[$i]['time_in'];
                $key_down = $array[$i]['key'];
                $priority = abs(($this->time_min + $this->time_max) - (2 * $time_in)); // суть метода - В приоритете события в середине дня. По мере движения в начало или конец дня, приоритет снижается
            }
            if ($time_in != $time_out) {
                $data[] = [
                    'key_up' => $key_up,
                    'time_in' => $time_in,
                    'time_out' => $time_out,
                    'time' => $time_out - $time_in,
                    'key_down' => $key_down,
                    'priority' => $priority,
                ];
            }
        }
        return $data;
    }

    protected function initKey()
    {
        $key = $key_name = [];
        $type = SubjectType::getTypeShortList();
        $dir = Direction::getDirectionShortList();
        $vid = DirectionVid::getDirectionVidShortList();

        foreach ($type as $subject_type_id => $type_name) {
            foreach ($dir as $direction_id => $direction_name) {
                foreach ($vid as $direction_vid_id => $direction_vid_name) {
                   // $key[$subject_type_id][$direction_id][$direction_vid_id] = 0;
                    $key[$subject_type_id . '|' . $direction_id . '|' . $direction_vid_id] = 0;
                    $key_name[$subject_type_id . '|' . $direction_id . '|' . $direction_vid_id] = ' ' . $direction_name  . ' ' . $direction_vid_name;
                }
            }
        }
        $this->key_name = $key_name;
        $this->keys = $key;
    }

    protected function getType($key)
    {
        $keys = explode('|', $key);
        return $keys[0];
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

        $auditory_up_time = $astr_time_summ = $this->keys;

        foreach ($array as $i => $item) {
            $time = $item['time_out'] - $item['time_in']; // длительность занятия (сек)
            $time_academ = Schedule::astr2academ($time);  // переводим в академические часы (час)
            $time_astr = $time_academ * 3600; // переводим академические часы в астронамические (суть - 1 академический час = 1 астронамический)

            if ($this->getType($item['key']) == 1001) {
                $auditory_up_time[$item['key']] = 0;     // Превышения на внебюджете нет. Считаем как есть.
                $astr_time_summ[$item['key']] += $time;  // Если внебюджет не переводим в астраномические часы, то и перерыв считаем без перевода
            } else {
                $auditory_up_time[$item['key']] += $time_astr - $time; // превышение астронамического времени над длительностью занятия (для добавления)
                $astr_time_summ[$item['key']] += $time_astr;
            }
        }
        $summ[1000] = $summ[1001] = 0;
        foreach ($astr_time_summ as $key => $value) {
            $summ[$this->getType($item['key'])] += $value;
        }
        arsort($auditory_up_time); // отсортируем по меньшему времени (первые заносим с меньшей нагрузкой)
        $pause_needs = [ // Для чего? : показываем перерыв только если выбран бюджет или внебюджет.
            1000 => $summ[1000] > $this->time_limit[1000] ? 1 : 0,
            1001 => $summ[1001] > $this->time_limit[1001] ? 1 : 0, // для внебюджета на 1 час больше
        ];
        $count_per = $pause_needs[1000] == 1 || $pause_needs[1001] == 1 ? 1 : 0;  // если больше 4-х часов, то 1 перерыв независимо от бюджета и внебюджета. Иначе - без перерыва
        $auditory_reserv_time = $this->day_time - ($summ[1000] + $summ[1001]) - ($this->time_per * $count_per); // оставшееся аудиторное время за вычетом перерывов
        $d = [
            'min_time' => $time_in,
            'max_time' => $time_out,
            'auditory_up_time' => $auditory_up_time, // array дополнительное время при пересчете по типу занятий
            'astr_time_summ' => $astr_time_summ,
            'pause_needs' => $pause_needs,
            'count_per' => $count_per,
            'auditory_reserv_time' => $auditory_reserv_time,
            'error_flag' => ($auditory_reserv_time + $this->error_time) < 0 ? true : false,
        ];
        // echo '<pre>' . print_r($d, true) . '</pre>';
        return $d;
    }

    /**
     * Генерация графика работы
     * @param $arrayMaster
     * @return array
     */
    protected function generatorWorkSchedule($teachers_id, $arrayMaster)
    {

        $analiticArray = $this->getScheduleAnalitic($arrayMaster);
//        $arrayMaster2 = $this->correctArray($arrayMaster);
//        $analiticArray2 = $this->getScheduleAnalitic($arrayMaster2);
        //  $analiticArray['auditory_up_time'] = $analiticArray2['auditory_up_time'];
        $freeArray = $this->getScheduleFreeItem($arrayMaster);
//        echo '<pre>' . print_r($analiticArray, true) . '</pre>';
//        echo '<pre>' . print_r($analiticArray2, true) . '</pre>';
//        die();
        if ($analiticArray['error_flag'] == true && $this->limit_up_flag) {
            $time = StringHelper::secondsToHoursMinutes(abs($analiticArray['auditory_reserv_time']));
            $arrayMaster[] = [
                'time_disp' => 'Превышение нагрузки на ' . $time,
            ];
            return $arrayMaster;
        }

        if ($analiticArray['count_per'] == 1) {
            array_multisort(array_column($freeArray, 'priority'), SORT_ASC, $freeArray); // сортируем массив по приоритету. Добиваемся того, чтобы перерыв оказался как можно ближе к середине дня.
            $this->setLunchBreak($freeArray, $arrayMaster);
        }
        $this->setUpTime($freeArray, $arrayMaster, $analiticArray);
        $d = $this->mergeMaster($arrayMaster);
//        echo '<pre>' . print_r($this, true) . '</pre>';
//        echo '<pre>' . print_r($this->activitiesArray, true) . '</pre>';
//        echo '<pre>' . print_r($d, true) . '</pre>';
//        die();

        return $this->normaliseArray($teachers_id, $d, $analiticArray); // нормализация
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
            if ($item['key_up'] == 0) { // Притягиваем снизу
                $time_in = $item['time_out'] - $this->time_per;
                $time_out = $item['time_out'];
                $free_update[] = [
                    'key_up' => $item['key_up'],
                    'time_in' => $item['time_in'],
                    'time_out' => $time_in,
                    'time' => $time_in - $item['time_in'],
                    'key_down' => $item['key_down'],
                    'priority' => $item['priority'],
                ];
                $lunch_break = [
                    'key' => 0, // обозначаем перерыв - индекс = 0
                    'time_in' => $time_in,
                    'time_out' => $time_out,
                ];
            } else { // Притягиваем сверху
                $time_in = $item['time_in'];
                $time_out = $item['time_in'] + $this->time_per;
                $free_update[] = [
                    'key_up' => $item['key_up'],
                    'time_in' => $time_out,
                    'time_out' => $item['time_out'],
                    'time' => $item['time_out'] - $time_out,
                    'key_down' => $item['key_down'],
                    'priority' => $item['priority'],
                ];
                $lunch_break = [
                    'key' => 0, // обозначаем перерыв - индекс = 0
                    'time_in' => $time_in,
                    'time_out' => $time_out,
                ];
            }

            if ($item['time'] > $this->time_per) {
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
        foreach ($analiticArray['auditory_up_time'] as $key => $time) {
            $this->sortFreeArrayForUpTime($freeArray);
            $t = $time;
            $clone = $freeArray;
            foreach ($clone as $i => $item) {
                if ($t <= 0) {
                    break;
                }
                if ($item['key_up'] == 0) { // Притягиваем снизу
                    $up_time = [
                        'key' => $key,
                        'time_in' => $t < $item['time'] ? $item['time_out'] - $t : $item['time_in'],
                        'time_out' => $item['time_out'],
                    ];
                    $update = [
                        'key_up' => $item['key_up'],
                        'time_in' => $item['time_in'],
                        'time_out' => $t < $item['time'] ? $item['time_out'] - $t : $item['time_in'],
                        'time' => $item['time'] - $t,
                        'key_down' => $item['key_down'],
                        'priority' => $item['priority'],
                    ];
                } else { // Притягиваем сверху
                    $up_time = [
                        'key' => $key,
                        'time_in' => $item['time_in'],
                        'time_out' => $t < $item['time'] ? $item['time_in'] + $t : $item['time_out'],
                    ];
                    $update = [
                        'key_up' => $item['key_up'],
                        'time_in' => $t < $item['time'] ? $item['time_in'] + $t : $item['time_out'],
                        'time_out' => $item['time_out'],
                        'time' => $item['time'] - $t,
                        'key_down' => $item['key_down'],
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
//                echo '<pre>' . print_r($arrayMaster, true) . '</pre>';
            }
        }
    }

    /**
     * Добиваемся того, что середина сортируется по времени остатка и приоритету, а концы останутся только по приоритету.
     * @param $freeArray
     */
    protected function sortFreeArrayForUpTime(&$freeArray)
    {
        $arrayA = [];
        $arrayB = [];

        array_map(function ($value) use (&$arrayA, &$arrayB) {
            if ($value['key_up'] == 0 || $value['key_down'] == 0) {
                $arrayA[] = $value;
            } else {
                $arrayB[] = $value;
            }
        }, $freeArray); // разделяем массивы : элементы по краям в один массив, середину в другой

        array_multisort(array_column($arrayB, 'time'), SORT_ASC, array_column($arrayB, 'priority'), SORT_ASC, $arrayB); // сортируем середину по остатку времени
        array_multisort(array_column($arrayA, 'priority'), SORT_ASC, $arrayA);
        $freeArray = array_merge($arrayB, $arrayA); // кидаем первый массив в конец второго
    }

    /**
     * формирование отчета
     * @throws \yii\base\InvalidConfigException
     */
    public function makeXlsx()
    {
        $data[] = [
            'rank' => 'doc',
            'type' => $this->subject_type_id == 1000 ? 'Бюджет' : 'Внебюджет',
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
//                'level_slug' => $this->teachers_level[$teachers_id],
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

