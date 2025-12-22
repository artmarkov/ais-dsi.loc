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
    protected $time_max;
    protected $time_limit;
    protected $time_per;
    protected $period_time = 300; // минимаольный шаг периода сек.

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
        $this->time_limit = Yii::$app->settings->get('module.generator_time_limit', 4) * 60 * 60;
        $this->time_per = Yii::$app->settings->get('module.generator_time_per', 30) * 60;
        $this->teachers_list = $model_date->teachers_list;
        $this->subject_type_flag = $model_date->subject_type_flag;
        $this->getTeacherInfo();
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

        $models = (new Query())->from('subject_schedule_view')
            ->select('teachers_id, week_day, SUM(time_out - time_in) as time, MIN(time_in) as min_time')
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => $this->subject_type_flag ? [1000, 1001] : 1000])
            ->andWhere(['status' => 1])
            ->groupBy('teachers_id, week_day')
            ->all();
        foreach ($models as $item => $data) {
            if (isset($data['week_day'])) {
                $time = Schedule::astr2academ($data['time']) * 3600; // приравниваем академический час к астрономическому
                $count_per = floor($time / $this->time_limit);
                $time_out_max = $data['min_time'] + $time + (($count_per > 1 ? $count_per - 1 : 1) * $this->time_per); // расчитываем максимальное время окончания с учетом перерывов
                $time_out_max = ceil($time_out_max/$this->period_time) * $this->period_time; // выравниваем по 5 - мин интервалу
                $min_time = $data['min_time'];
                if ($time_out_max > $this->time_max) {
                   $n = ceil(($time_out_max - $this->time_max)/$this->period_time); // выравниваем по 5 - мин интервалу
                    $min_time = $min_time - ($n * $this->period_time);
                    $time_out_max = $this->time_max;
                }
                $data_schedule[$data['teachers_id']][$data['week_day']]['time'] = $time;
                $data_schedule[$data['teachers_id']][$data['week_day']]['min_time'] = $min_time;
                $data_schedule[$data['teachers_id']][$data['week_day']]['max_time'] = $time_out_max;
                $data_schedule[$data['teachers_id']][$data['week_day']]['count_per'] = $count_per;
                $d = [];
                for ($i = 1; $i <= $count_per + 1; $i++) {
                    if ($time_out_max == $min_time) break;
                    $time_out = $min_time + $this->time_limit;
                    if ($time_out > $time_out_max) $time_out = $time_out_max;
                    $d[$i]['time'] = [$min_time, $time_out];
                    $d[$i]['time_disp'] = Schedule::decodeTime($min_time) . '-' . Schedule::decodeTime($time_out);
                    $time_per_out = $time_out + $this->time_per;
                    if ($time_per_out > $time_out_max) break;
                    $d[$i]['time_per'] = [$time_out, $time_per_out];
                    $d[$i]['time_per_disp'] = Schedule::decodeTime($time_out) . '-' . Schedule::decodeTime($time_per_out);
                    $min_time = $time_per_out;
                }
                $data_schedule[$data['teachers_id']][$data['week_day']]['schedule'] = $d;
            }
        }
//        echo '<pre>' . print_r($data_schedule, true) . '</pre>';
        return $data_schedule;
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

