<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\Schedule;
use common\models\guidejob\Bonus;
use common\models\user\UserCommon;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class TarifStatement
{
    const template_timesheet = 'document/tarif_statement.xlsx';

    protected $plan_year;
    protected $timestamp_in;
    protected $timestamp_out;

    protected $activity_list;
    protected $teachers_list;
    protected $teachers_bonus;
    protected $bonus_name;
    protected $teachers_schedule_total;
    protected $teachers_consult_total;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $timestamp = ArtHelper::getStudyYearParams($this->plan_year);
        $this->timestamp_in = $timestamp['timestamp_in'];
        $this->timestamp_out = $timestamp['timestamp_out'];
        $this->activity_list = $this->getTeachersActivities();
        $this->teachers_list = $this->getTeachersList();
        $this->teachers_bonus = $this->getTeachersBonus();
        $this->bonus_name = $this->getBonusName();
        $this->getTeachersLoad();
        // $this->teachers_schedule_total = $this->getTeacherScheduleTotal();
        // $this->teachers_consult_total = $this->getTeachersConsult();
    }


    protected function getTeachersActivities()
    {
        $models = TeachersActivityView::find()
            ->where(['user_common_status' => UserCommon::STATUS_ACTIVE])
            ->orderBy('last_name, first_name, middle_name, direction_id, direction_vid_id')
            ->all();
        return $models;
    }

    protected function getTeachersList()
    {
        $teachers_list = [];
        foreach ($this->activity_list as $model) {
            $teachers_list[] = $model->teachers_id;
        }
        return array_unique($teachers_list);
    }
//
//    /**
//     * Фактическое отработанное время
//     * @return array
//     */
//    protected function getTeachersScheduleTotal()
//    {
//        $data_schedule_total = [];
//
//        $models_total = (new Query())->from('subject_schedule_view')
//            ->select('direction_id, direction_vid_id, teachers_id, subject_type_id, SUM(time_out-time_in) as time')
//            ->where(['teachers_id' => $this->teachers_list])
//            ->andWhere(['plan_year' => $this->plan_year])
//            ->andWhere(['status' => 1])
//            ->groupBy('direction_id, direction_vid_id, teachers_id, subject_type_id')
//            ->all();
//
//        foreach ($models_total as $item => $data) {
//            $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']] = isset($data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']]) ? $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']] + Schedule::astr2academ($data['time']) : Schedule::astr2academ($data['time']);
//        }
//        return $data_schedule_total;
//    }
//
//    /**
//     * Фактическое отработанное время консультаций
//     * @return array
//     */
//    protected function getTeachersConsult()
//    {
//        $data_schedule_total = [];
//        $models = (new Query())->from('consult_schedule_view')
//            ->select('direction_id, direction_vid_id, teachers_id, subject_type_id, SUM(datetime_out-datetime_in) as time')
//            ->where(['teachers_id' => $this->teachers_list])
//            ->andWhere(['plan_year' => $this->plan_year])
//            ->andWhere(['status' => 1])
//            ->groupBy('direction_id, direction_vid_id, teachers_id, subject_type_id')
//            ->all();
//        foreach ($models as $item => $data) {
//            $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']] = isset($data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']]) ? Schedule::astr2academ($data['time']) + $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']] : Schedule::astr2academ($data['time']);
//        }
//
////        echo '<pre>' . print_r($data_schedule_total, true) . '</pre>';
//        return $data_schedule_total;
//    }

    /**
     * Планируемое время нагрузки преподавателя
     * @return array
     */
    protected function getTeachersLoad()
    {
        $data_load = $data_load_cons = [];
        $models = (new Query())->from('teachers_load_view')
            ->select('direction_id, direction_vid_id, teachers_id, subject_type_id, load_time, load_time_consult')
            ->where(['teachers_id' => $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['status' => 1])
            ->all();
        foreach ($models as $item => $data) {
            $data_load[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']] = isset($data_load[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']]) ? $data_load[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']] + $data['load_time'] : $data['load_time'];
            $data_load_cons[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']] = isset($data_load_cons[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']]) ? $data_load_cons[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['subject_type_id']] + $data['load_time_consult'] : $data['load_time_consult'];
        }
        $this->teachers_schedule_total = $data_load;
        $this->teachers_consult_total = $data_load_cons;
//        echo '<pre>' . print_r($data_load, true) . '</pre>';
//        echo '<pre>' . print_r($data_load_cons, true) . '</pre>';
    }

    protected function getTeachersBonus()
    {
        $models = Teachers::find()
            ->select('id as teachers_id, bonus_list, bonus_summ, bonus_summ_abs')
            ->where(['in', 'id', $this->teachers_list])
            ->asArray()
            ->all();
        $models = ArrayHelper::index($models, 'teachers_id');
//        echo '<pre>' . print_r($models, true) . '</pre>';
        return $models;
    }

    protected function getBonusName()
    {
        $models = ArrayHelper::map(Bonus::find()->all(), 'id', 'slug');
//        echo '<pre>' . print_r($models, true) . '</pre>';
        return $models;
    }

    protected function getTeachersBonusSumm($direction_vid_id, $teachers_id, $stake_value)
    {
        if ($direction_vid_id == 1000) {
            return ($this->teachers_bonus[$teachers_id]['bonus_summ'] * $stake_value * 0.01) + $this->teachers_bonus[$teachers_id]['bonus_summ_abs'];
        }
        return '';
    }

    protected function getTeachersBonusSlug($direction_vid_id, $teachers_id)
    {
        $array = [];
        if ($direction_vid_id == 1000) {
            $bonus = explode(',', $this->teachers_bonus[$teachers_id]['bonus_list']);
            foreach ($bonus as $item => $id) {
                $array[] = $this->bonus_name[$id] ?? '';
            }
        }
        return implode(' ', $array);
    }

    protected function getData($subject_type_id)
    {
        $items = [];
        $index = 0;
        $teachersIdConst = null;
        foreach ($this->activity_list as $item => $d) {
            $bonus_summ = $this->getTeachersBonusSumm($d->direction_vid_id, $d->teachers_id, $d->stake_value);
            $load_time_teach = $this->teachers_schedule_total[1000][$d->direction_vid_id][$d->teachers_id][$subject_type_id] ?? 0;
            $load_time_conс = $this->teachers_schedule_total[1001][$d->direction_vid_id][$d->teachers_id][$subject_type_id] ?? 0;
            $load_year_teach = $this->teachers_consult_total[1000][$d->direction_vid_id][$d->teachers_id][$subject_type_id] ?? 0;
            $load_year_conс = $this->teachers_consult_total[1001][$d->direction_vid_id][$d->teachers_id][$subject_type_id] ?? 0;

            if (($load_time_teach + $load_time_conс) != 0) {
                $indexItem = '';
                if ($d->teachers_id != $teachersIdConst) {
                    $index++;
                    $indexItem = $index . '.';
                }
                $teachersIdConst = $d->teachers_id;
                $items[] = [
                    'rank' => 'a',
                    'item' => $indexItem,
                    'last_name' => $d->last_name,
                    'first_name' => $d->first_name,
                    'middle_name' => $d->middle_name,
                    'level_name' => $d->level_slug,
                    'year_serv_spec' => $d->year_serv_spec,
                    'stake_slug' => $d->stake_slug,
                    'direction_slug' => $d->direction_slug,
                    'slug' => $d->work_id == 1001 ? $d->work_slug : ($d->direction_vid_id != 1000 ? $d->direction_vid_slug : ''),
                    'stake_teach' => $d->direction_id == 1000 ? $d->stake_value : '',
                    'stake_conс' => $d->direction_id == 1001 ? $d->stake_value : '',
                    'load_time_teach' => $load_time_teach,
                    'load_time_conс' => $load_time_conс,
                    'load_year_teach' => $load_year_teach,
                    'load_year_conс' => $load_year_conс,
                    'bonus_summ' => $bonus_summ,
                    'bonus_list' => $bonus_summ != '' ? $this->getTeachersBonusSlug($d->direction_vid_id, $d->teachers_id) : '',
                ];
            }
        }
        //        print_r($items); die();
        return $items;
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

        $output_file_name = str_replace('.', '_' . Yii::$app->formatter->asDate(time(), 'php:Y_m_d H_i') . '.', basename(self::template_timesheet));

        $tbs = DocTemplate::get(self::template_timesheet)->setHandler(function ($tbs) use ($data) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('a', $this->getData(1000));
            $tbs->PlugIn(OPENTBS_SELECT_SHEET, 2);
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('a', $this->getData(1001));

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

}

