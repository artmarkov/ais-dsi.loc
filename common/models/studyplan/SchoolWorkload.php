<?php

namespace common\models\studyplan;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\Schedule;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class SchoolWorkload
{
    const template = 'document/report_form_workload.xlsx';

    protected $plan_year;
    protected $template_name;

    const num_week = 32;
    const programm_rr = [1030, 1041, 1044, 1050, 1051, 1052, 1053];

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->template_name = self::template;
    }

    protected function getSchedules()
    {
        $models = (new Query())->from('subject_schedule_view')
            ->select('auditory_id, programm_list, 
            SUM(time_out-time_in) AS sum,
            SUM(CASE WHEN time_in >= 21600 AND time_in < 36000 THEN time_out-time_in END) AS sum_9_13,
            SUM(CASE WHEN time_in >= 36000  AND time_in < 54000 THEN time_out-time_in END) AS sum_13_18,
            SUM(CASE WHEN time_in >= 54000  AND time_in <= 64800 THEN time_out-time_in END) AS sum_18_21
            ')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['IS NOT', 'subject_schedule_id', NULL])
            ->andWhere(['status' => 1])
            ->andWhere(['direction_id' => 1000])
            ->groupBy('auditory_id, programm_list')
            ->all();

        return $models;
    }

    protected function getActivities()
    {
        $timestamp = ArtHelper::getStudyYearParams($this->plan_year);
        $models = (new Query())->from('activities_view')
            ->select(new \yii\db\Expression('auditory_id, to_char(to_timestamp(start_time)::date, \'IW\')::int AS week_num,
            SUM(end_time-start_time)  AS sum'))
            ->where(['between', 'start_time', $timestamp['timestamp_in'], $timestamp['timestamp_out']])
            ->andWhere(['category_id' => [1003, 1004, 1005]])
            ->andWhere(['OR', ['direction_id' => 1000], ['IS', 'direction_id', NULL]])
            ->groupBy('auditory_id, week_num')
            ->all();

        return $models;
    }

    public function getData()
    {
        $data = [];
        $data['rank'] = 'doc';
        $mask = [
            '1' => [[1015, 1016, 1017, 1018, 1020, 1032, 1033, 1034, 1045, 1046], 6, 'Класс специальности и ансамбля', 'Индивидуальные и мелкогрупповые от 2-х человек'],
            '2' => [[1021, 1023, 1024, 1025, 1026, 1027, 1028, 1029, 1030, 1037, 1039, 1040, 1041, 1042, 1043, 1044, 1045, 1046, 1047], 6, 'Многофункциональный класс', 'Индивидуальные и мелкогрупповые от 2-х человек'],
            '3' => [[1007, 1008, 1009, 1012, 1013], 6, 'Кабинет теоретических дисциплин', 'Мелкогрупповые от 4-х до 10 человек'],
            '4' => [[1031, 1010], 6, 'Хоровой класс', 'Групповые занятий от 11 человек и мелкогрупповые от 4-х до 10 человек'],
            '5' => [[1002, 1003, 1004, 1005, 1006, 1035, 1036, 1058, 1059, 1060, 1061, 1065, 1066, 1067, 1068, 1069, 1072], 6, 'Класс изобразительного искусства', 'Групповые занятий от 11 человек,  мелкогрупповые от 4-х до 10 человек'],
            '6' => [[1038, 1040], 5, 'Класс хореографии', 'Групповые занятий от 11 человек,  мелкогрупповые от 4-х до 10 человек'],
            '7' => [[1001], 4, 'Малый зал', 'Все'],
            '8' => [[1000], 4, 'Большой зал', 'Все'],

        ];
        $dataSchedules = ArrayHelper::index($this->getSchedules(), null, ['auditory_id']);
        //  echo '<pre>' . print_r(Schedule::encodeTime('18:00'), true) . '</pre>'; die();
        $dataActivities = ArrayHelper::index($this->getActivities(), 'week_num', ['auditory_id']);
        //echo '<pre>' . print_r($dataActivities, true) . '</pre>';die();
        foreach ($mask as $index => $array) {
            $sum = 0;
            foreach ($array[0] as $value) {
                if (isset($dataActivities[$value])) {
                    $sum += self::findMax($dataActivities[$value]);
                }
            }
            $data[$index . '_activities_all'] = round($sum / 3600);
        }

        foreach ($mask as $index => $array) {
            $sum = 0;
            $sum_act = 0;
            $sum_9_13 = $sum_13_18 = $sum_18_21 = 0;
            $sum_free_9_13 = $sum_free_13_18 = $sum_free_18_21 = 0;
            $sum_9_13_all = $sum_13_18_all = $sum_18_21_all = 0;
            $data[$index . '_num'] = $index . '.';
            $data[$index . '_name'] = $array[2];
            $data[$index . '_cat'] = $array[3];
            $data[$index . '_workday_count'] = $array[1];
            $data[$index . '_aud_count'] = count($array[0]);
            foreach ($array[0] as $value) {
                $sum_free_9_13 += 14400;
                $sum_free_13_18 += 18000;
                $sum_free_18_21 += 10800;
                if (isset($dataSchedules[$value])) {
                    foreach ($dataSchedules[$value] as $val) {
                        $sum_9_13_all += $val['sum_9_13'];
                        $sum_13_18_all += $val['sum_13_18'];
                        $sum_18_21_all += $val['sum_18_21'];
                        $sum += $val['sum'];
                        if (!empty(array_intersect(self::programm_rr, explode(',', $val['programm_list'])))) {
                            $sum_9_13 += $val['sum_9_13'];
                            $sum_13_18 += $val['sum_13_18'];
                            $sum_18_21 += $val['sum_18_21'];
                        }
                    }
                }
                if (isset($dataActivities[$value])) {
                    $sum_act += self::findMax($dataActivities[$value]);
                }
            }
            $data[$index . '_activities_all'] = round($sum_act / 3600);

            $data[$index . '_time_one_day'] = round(($sum - ($sum_9_13 + $sum_13_18 + $sum_18_21)) / 3600 / count($array[0]) / $array[1]);
            $data[$index . '_time_all_9_13'] = round($sum_9_13 / 3600);
            $data[$index . '_time_all_13_21'] = round(($sum_13_18 + $sum_18_21) / 3600);

            $data[$index . '_time_free_9_13'] = round(($sum_free_9_13 * $array[1] - $sum_9_13_all) / 3600);
            $data[$index . '_time_free_13_18'] = round(($sum_free_13_18 * $array[1] - $sum_13_18_all) / 3600);
            $data[$index . '_time_free_18_21'] = round(($sum_free_18_21 * $array[1] - $sum_18_21_all) / 3600);
        }


        // echo '<pre>' . print_r($spec, true) . '</pre>';
//         echo '<pre>' . print_r($data, true) . '</pre>';die();
        //echo '<pre>' . print_r($spec, true) . '</pre>';
        return $data;
    }

    protected static function findMax($arr)
    {
        $max = null;
        foreach ($arr as $index => $val) {
            $tmp = $val['sum'];
            if ($tmp > $max)
                $max = $tmp;
        }
        return $max;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function makeXlsx()
    {
        $data[] = $this->getData();
        $output_file_name = Yii::$app->formatter->asDate(time(), 'php:Y-m-d_H-i-s') . '_' . basename($this->template_name);
        $tbs = DocTemplate::get($this->template_name)->setHandler(function ($tbs) use ($data) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

}
