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
                echo '<pre>' . print_r($this->generatorSchedule($array), true) . '</pre>';
        die();
            }
        }
        foreach ($models as $item => $data) {
            if (isset($data['week_day'])) {
                $time = Schedule::astr2academ($data['time']) * 3600; // приравниваем академический час к астрономическому
                if ($time >= $this->time_limit) { // если больше 4-х часов, то 1 перерыв. Иначе - без перерыва
                    $count_per = 1;
                    $time_limit = $time / 2;
                    $time_limit = ceil($time_limit / $this->period_time) * $this->period_time; // выравниваем по 5 - мин интервалу
                } else {
                    $count_per = 0;
                    $time_limit = $time;
                }
                $full_time = $time + ($count_per * $this->time_per);

                $time_out_max = $data['min_time'] + $full_time; // расчитываем максимальное время окончания с учетом перерывов
                $time_out_max = ceil($time_out_max / $this->period_time) * $this->period_time; // выравниваем по 5 - мин интервалу

                $min_time = $data['min_time'];
                if ($time_out_max > $this->time_max) {
                    $n = ceil(($time_out_max - $this->time_max) / $this->period_time); // выравниваем по 5 - мин интервалу
                    $min_time = $min_time - ($n * $this->period_time);
                    $time_out_max = $this->time_max;
                }
                $data_schedule[$data['teachers_id']][$data['week_day']]['time'] = $time;
                $data_schedule[$data['teachers_id']][$data['week_day']]['min_time'] = $min_time;
                $data_schedule[$data['teachers_id']][$data['week_day']]['max_time'] = $time_out_max;
                $data_schedule[$data['teachers_id']][$data['week_day']]['count_per'] = $count_per;

                $d = [];
                $per = 0;
                for ($i = 1; $i <= $count_per + 1; $i++) {
                    if ($this->limit_up_flag && ($full_time > ($this->time_max - $this->time_min))) {
                        $d[$i]['time_disp'] = 'Превышение нагрузки на ' . round(($full_time - ($this->time_max - $this->time_min)) / 3600, 1) . ' ч.';
                        break;
                    }
                    if ($time_out_max == $min_time) break;
                    $time_out = $min_time + $time_limit;
                    if ($time_out > $time_out_max) $time_out = $time_out_max;
                    $d[$i]['time'] = [$min_time, $time_out];
                    if ($i == $count_per + 1) {
                        $time_out = $time_out < $data['max_time'] ? $data['max_time'] : $time_out_max;
                    }
                    $d[$i]['time_disp'] = Schedule::decodeTime($min_time) . '-' . Schedule::decodeTime($time_out);
                    $time_per_out = $time_out + $this->time_per;
                    if ($time_per_out > $time_out_max) {
                        break;
                    }
                    if ($per == 0) {
                        $d[$i]['time_per'] = [$time_out, $time_per_out, $time_out_max];
                        $d[$i]['time_per_disp'] = Schedule::decodeTime($time_out) . '-' . Schedule::decodeTime($time_per_out);
                        $min_time = $time_per_out;
                        $per = 1;
                    }
                }
                $data_schedule[$data['teachers_id']][$data['week_day']]['schedule'] = $d;

            }
        }
//        echo '<pre>' . print_r($data_schedule, true) . '</pre>';
        return $data_schedule;
    }

    /**
     * Формируем новый массив данных расписания на день, группируя данные по порядку и по типу обучения
     * @param $array
     * @param $factor
     * @return array
     */
    protected function mergeScheduleItem($array, $factor = true)
    {
        $const = null;
        $i = 0;
        $d = [];
        foreach ($array as $item => $data) {
            if ($item == 0) {
                $d[$i]['subject_type_id'] = $data['subject_type_id'];
                $d[$i]['time_in'] = $data['time_in'];
                $d[$i]['time_out'] = $data['time_in'] + $this->getPeriod($data['time_in'], $data['time_out'], $factor);
            } else {
                if ($data['subject_type_id'] == $const) {
                    $d[$i]['time_out'] = $d[$i]['time_out'] + $this->getPeriod($data['time_in'], $data['time_out'], $factor);
                } else {
                    $i++;
                    $d[$i]['subject_type_id'] = $data['subject_type_id'];
                    $d[$i]['time_in'] = $d[$i - 1]['time_out'];
                    $d[$i]['time_out'] = $d[$i - 1]['time_out'] + $this->getPeriod($data['time_in'], $data['time_out'], $factor);
                }
            }
            $const = $data['subject_type_id'];
            $d[$i]['time_disp'] = Schedule::decodeTime($d[$i]['time_in']) . '-' . Schedule::decodeTime($d[$i]['time_out']);
        }
        return $d;
    }

    protected function getScheduleFreeItem($array)
    {
        $d = [];
        for ($item = 0; $item <= count($array); $item++) {
            if ($item == 0) {
                $d[$item]['subject_type_up'] = 0;
                $d[$item]['time_in'] = $this->time_min;
                $d[$item]['time_out'] = $array[$item]['time_in'];
                $d[$item]['subject_type_down'] = $array[$item]['subject_type_id'];
            } elseif ($item == count($array)) {
                $d[$item]['subject_type_up'] = $array[$item-1]['subject_type_id'];
                $d[$item]['time_in'] = $array[$item-1]['time_out'];
                $d[$item]['time_out'] = $this->time_max;
                $d[$item]['subject_type_down'] = 0;
            } else {
                $d[$item]['subject_type_up'] = $array[$item-1]['subject_type_id'];
                $d[$item]['time_in'] = $array[$item-1]['time_out'];
                $d[$item]['time_out'] = $array[$item]['time_in'];
                $d[$item]['subject_type_down'] = $array[$item]['subject_type_id'];
                }
            }
        return $d;
    }

    protected function getScheduleAnalitic($array)
    {
        $time_in = min(array_column($array, 'time_in'));
        $time_out = max(array_column($array, 'time_out'));
       // $school_time = $time_out - $time_in; // полное время нахождения преподавателя в школе
        echo '<pre>' . print_r($array, true) . '</pre>';
        $auditory_time[1000] = array_reduce($array, function ($summ, $item) {
            if ($item['subject_type_id'] == 1000){
            return $summ + ($item['time_out'] - $item['time_in']);
            }
        });
        $auditory_time[1001] = array_reduce($array, function ($summ, $item) {
            if ($item['subject_type_id'] == 1001){
                return $summ + ($item['time_out'] - $item['time_in']);
            }
        }); // сумма аудиторных академ часов
        echo '<pre>' . print_r($auditory_time, true) . '</pre>'; die();
        $auditory_up_time = $auditory_time / 3; // время превышения при пересчете академ на астрономич аудиторного времени
        $count_per = ($auditory_time + $auditory_up_time) >= $this->time_limit ? 1 : 0;  // если больше 4-х часов, то 1 перерыв. Иначе - без перерыва
        $auditory_reserv_time = $this->day_time - $auditory_time - $auditory_up_time - ($this->time_per * $count_per); // оставшееся аудиторное время за вычетом перерывов
      //  $school_reserv_time = $this->day_time - $school_time - $auditory_up_time - ($this->time_per * $count_per); // оставшееся школьное время за вычетом перерывов

        $algoritm = 1;
        if ($auditory_reserv_time < 0) {
            $info = 'на доработку';
            $algoritm = 0;
        } /*elseif ($school_reserv_time < 0) {
            $algoritm = 1;
            $info = 'алгоритм последовательного пересчета';
        } else {
            $algoritm = 2;
            $info = 'алгоритм перестановки';
        }*/

        $d = [
//            'teachers_id' => $array[0]['teachers_id'],
//            'week_day' => $array[0]['week_day'],
            'min_time' => $time_in,
            'max_time' => $time_out,
//            'school_time' => $school_time,
            'auditory_time' => $auditory_time,
            'auditory_up_time' => $auditory_up_time,
            'pause_needs' => $count_per,
            'auditory_reserv_time' => $auditory_reserv_time,
//            'school_reserv_time' => $school_reserv_time,
            'algoritm' => $algoritm,
//            'info' => $info
        ];

        return $d;
    }

    protected function generatorSchedule($array) {

        $analiticArray = $this->getScheduleAnalitic($array);
        $freeArray = $this->getScheduleFreeItem($array);
        if($analiticArray['algoritm'] == 0) {
            // на доработку
            return;
        }
//        $scheduleArray = $this->mergeScheduleItem($array);
//        if($analiticArray['pause_needs'] == 1) { // ставим перерыв в начало
//            $time_in = $analiticArray['min_time'] - $this->time_per;
//            $time_out = $analiticArray['min_time'];
//            $array = [
//            'subject_type_id' => 1000,
//            'time_in' => $time_in,
//            'time_out' => $time_out,
//            'time_per_disp' => Schedule::decodeTime($time_in) . '-' . Schedule::decodeTime($time_out),
//            ];
//            array_unshift($scheduleArray , $array);
//        }
        echo '<pre>' . print_r($analiticArray, true) . '</pre>';
      //  echo '<pre>' . print_r($freeArray, true) . '</pre>';
    }

    public static function getTypeList() {
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
        $time = ceil($time / $this->period_time) * $this->period_time; // выравниваем по 5 - мин интервалу
        return $time;
    }

    /**
     * формирование документов: Тарификационная ведомость
     *
     * @param $template
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

