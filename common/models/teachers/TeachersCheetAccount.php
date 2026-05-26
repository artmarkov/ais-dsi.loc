<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use artsoft\widgets\Notice;
use common\models\routine\Routine;
use common\models\studyplan\StudyplanSubjectHist;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class TeachersCheetAccount
{
    protected $timestamp_in;
    protected $timestamp_out;
    protected $teachers_id;
    protected $plan_year;
    protected $disorder_teach;
    protected $disorder_cons;
    protected $is_disorder = [];

    public function __construct($model_date)
    {
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $this->timestamp_in = $timestamp[0];
        $this->timestamp_out = $timestamp[1];
        $this->plan_year = ArtHelper::getStudyYearDefault(null, $this->timestamp_in);
        $this->teachers_id = $model_date->teachers_id;
        $this->disorder_teach = $this->getTeacherDisorders();
        $this->disorder_cons = $this->getConsultDisorders();
//        print_r($model_date);
    }

    protected function getTeacherDisorders()
    {
        $data = [];
        $user_id = RefBook::find('teachers_users')->getValue($this->teachers_id);
        $models = (new Query())->from('activities_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'teachers_id', 'subject_type_id', 'datetime_in', 'datetime_out'])
            ->where(['teachers_id' => $this->teachers_id])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['status' => 1])
            ->all();
        foreach ($models as $i => $model) {
            $isDisorder = Routine::isDisorder($model['datetime_in'], $user_id);
            if ($isDisorder) {
                $this->is_disorder[] = date('j', $model['datetime_in']) . ' ' . ArtHelper::getMonthsList()[date('n', $model['datetime_in'])];
                if (isset($data[$model['studyplan_subject_id']][$model['subject_sect_studyplan_id']][$model['direction_id']][$model['direction_vid_id']][$model['subject_type_id']])) {
                    $data[$model['studyplan_subject_id']][$model['subject_sect_studyplan_id']][$model['direction_id']][$model['direction_vid_id']][$model['subject_type_id']] += Schedule::astr2academ($model['datetime_out'] - $model['datetime_in']);
                } else {
                    $data[$model['studyplan_subject_id']][$model['subject_sect_studyplan_id']][$model['direction_id']][$model['direction_vid_id']][$model['subject_type_id']] = Schedule::astr2academ($model['datetime_out'] - $model['datetime_in']);
                }
            }
        }
        // echo '<pre>' . print_r($data, true) . '</pre>'; die();
        return $data;
    }

    protected function getConsultDisorders()
    {
        $data = [];
        $user_id = RefBook::find('teachers_users')->getValue($this->teachers_id);
        $models = (new Query())->from('consult_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'teachers_id', 'subject_type_id', 'datetime_in', 'datetime_out'])
            ->where(['teachers_id' => $this->teachers_id])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['status' => 1])
            ->all();
        foreach ($models as $i => $model) {
            $isDisorder = Routine::isDisorder($model['datetime_in'], $user_id);
            if ($isDisorder) {
                $this->is_disorder[] = date('j', $model['datetime_in']) . ' ' . ArtHelper::getMonthsList()[date('n', $model['datetime_in'])];
                if (isset($data[$model['studyplan_subject_id']][$model['subject_sect_studyplan_id']][$model['direction_id']][$model['direction_vid_id']][$model['subject_type_id']])) {
                    $data[$model['studyplan_subject_id']][$model['subject_sect_studyplan_id']][$model['direction_id']][$model['direction_vid_id']][$model['subject_type_id']] += Schedule::astr2academ($model['datetime_out'] - $model['datetime_in']);
                } else {
                    $data[$model['studyplan_subject_id']][$model['subject_sect_studyplan_id']][$model['direction_id']][$model['direction_vid_id']][$model['subject_type_id']] = Schedule::astr2academ($model['datetime_out'] - $model['datetime_in']);
                }
            }
        }
        // echo '<pre>' . print_r($data, true) . '</pre>'; die();
        return $data;
    }

    public function getTeachersCheetData()
    {
        $data = [];
        $directions = \Yii::$app->db->createCommand('SELECT 
                          concat(guide_teachers_direction.id, guide_teachers_direction_vid.id) as id,
                          concat(guide_teachers_direction.slug, guide_teachers_direction_vid.slug) as name
	            FROM guide_teachers_direction, guide_teachers_direction_vid;'
        )->queryAll();
        $directions = ArrayHelper::index($directions, 'id');

        $attributes = ['subject' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['sect_name' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['subject_type_id' => Yii::t('art/guide', 'Subject Type'),];
        $attributes += $directions;
        // Бюджет - по нагрузке за неделю
        $models0 = (new Query())->from('teachers_load_view')
            ->select('studyplan_subject_id, subject_sect_studyplan_id, direction_id, direction_vid_id, teachers_id, subject_type_id, SUM(load_time) as time')
            ->where(['teachers_id' => $this->teachers_id])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => 1000])
            ->andWhere(['status' => 1])
            ->groupBy('studyplan_subject_id, subject_sect_studyplan_id, direction_id, direction_vid_id, teachers_id, subject_type_id')
            ->all();

        $models0 = ArrayHelper::index($models0, null, ['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id']);

        // Внебюджет
        $models1 = (new Query())->from('activities_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'teachers_id', 'subject_type_id', 'datetime_in', 'datetime_out'])
            ->where(['teachers_id' => $this->teachers_id])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => 1001])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['status' => 1])
            ->all();

        $models1 = ArrayHelper::index($models1, null, ['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id']);

        $modelsConsult = (new Query())->from('consult_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'teachers_id', 'subject_type_id', 'datetime_in', 'datetime_out'])
            ->where(['teachers_id' => $this->teachers_id])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['status' => 1])
            ->all();
        $modelsConsult = ArrayHelper::index($modelsConsult, null, ['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id']);

        $modelsLoad = (new Query())->from('subject_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id', 'sect_name', 'subject'])
            ->distinct()
            ->where(['teachers_id' => $this->teachers_id])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['status' => 1])
            ->andWhere(['not in', 'studyplan_subject_id', StudyplanSubjectHist::getStudyplanSubjectPass($this->timestamp_in)])
            ->all();

        foreach ($modelsLoad as $i => $items) {
            $data[$i]['subject'] = $items['subject'];
            $data[$i]['sect_name'] = $items['sect_name'];
            $data[$i]['subject_type_id'] = $items['subject_type_id'];
            // согласно расписанию
            if ($items['subject_type_id'] == 1000) {
                if (isset($models0[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                    foreach ($models0[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']] as $k => $time) {
                        if (isset($data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'])) {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] += $time['time'] * 4;
                        } else {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] = $time['time'] * 4;
                        }
                    }
                }
            } else {
                if (isset($models1[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                    foreach ($models1[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']] as $k => $time) {
                        if (isset($data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'])) {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] += Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                        } else {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] = Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                        }
                    }
                }
            }
            // консультации
            if (isset($modelsConsult[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                $label = [];
                foreach ($modelsConsult[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']] as $k => $time) {
                    if (isset($data[$i][$items['direction_id'] . $items['direction_vid_id']]['cons'])) {
                        $data[$i][$items['direction_id'] . $items['direction_vid_id']]['cons'] += Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                    } else {
                        $data[$i][$items['direction_id'] . $items['direction_vid_id']]['cons'] = Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                    }
                    $label[] = Yii::$app->formatter->asDatetime($time['datetime_in']) . ' - ' . Yii::$app->formatter->asDatetime($time['datetime_out']);
                }
                $data[$i][$items['direction_id'] . $items['direction_vid_id']]['title'] = implode(',', $label);
            }
            // корректировка нагрузки в связи с больничным листом
            if (isset($this->disorder_teach[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] -= $this->disorder_teach[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']];
                if ($data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] < 0) $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] = 0;
            }
            if (isset($this->disorder_cons[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                $data[$i][$items['direction_id'] . $items['direction_vid_id']]['cons'] -= $this->disorder_cons[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']];
                if ($data[$i][$items['direction_id'] . $items['direction_vid_id']]['cons'] < 0) $data[$i][$items['direction_id'] . $items['direction_vid_id']]['cons'] = 0;
            }
        }
        return ['data' => $data, 'attributes' => $attributes, 'directions' => $directions, 'is_disorder' => implode(', ', array_unique($this->is_disorder))];
    }

    public static function getTotal($provider, $fieldName)
    {
        $total[0]['teach'] = $total[0]['cons'] = $total[1]['teach'] = $total[1]['cons'] = 0;
        foreach ($provider as $item) {
            if ($item['subject_type_id'] == 1000) {
                $total[0]['teach'] += $item[$fieldName]['teach'] ?? 0;
                $total[0]['cons'] += $item[$fieldName]['cons'] ?? 0;
            } else {
                $total[1]['teach'] += $item[$fieldName]['teach'] ?? 0;
                $total[1]['cons'] += $item[$fieldName]['cons'] ?? 0;
            }
        }

        return $total[0]['teach'] . '/' . $total[0]['cons'] . '<span class="pull-right">' . $total[1]['teach'] . '/' . $total[1]['cons'] . '</span>';
    }

    protected function getTeachersScheduleNeed($teachers_id)
    {
        return (new Query())->from('subject_schedule_view')
            ->where(['teachers_id' => $teachers_id])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['status' => 1])
            ->andWhere(['IS', 'subject_schedule_id', null])
            ->all();
    }

    public function geTeachersScheduleNeedNotice($teachers_id)
    {
        $models = $this->getTeachersScheduleNeed($teachers_id);
        if ($models) {
            $message = '<b>Некорректное отображение табеля. Не заполнено расписание занятий</b>';
            $info = [];
            foreach ($models as $item => $itemModel) {
                $string = ($item + 1) . '. ' . $itemModel['subject'] . ' ' . $itemModel['sect_name'];
                $info[] = $string;
            }
            Notice::registerWarning($message . ':<br/>' . implode(',<br/>', $info));
        }
    }
}

