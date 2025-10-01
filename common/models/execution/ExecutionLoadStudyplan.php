<?php

namespace common\models\execution;

use common\models\education\EducationProgramm;
use common\models\studyplan\Studyplan;
use common\models\teachers\TeachersLoadStudyplanView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class ExecutionLoad
 * @package common\models\execution
 *
 */
class ExecutionLoadStudyplan
{
    protected $plan_year;
    protected $bad_flag;
    protected $programm_id;
    protected $education_cat_id;
    protected $teachersLoad;
    protected $models;

    public static function getData($model_date)
    {
        return new self($model_date);
    }

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->bad_flag = $model_date->bad_flag;
        $this->education_cat_id = $model_date->education_cat_id;
        $this->programm_id = $this->getProgramm($model_date);
        $this->teachersLoad = $this->getTeachersLoadStudyplan();
//        echo '<pre>' . print_r($this->programm_id, true) . '</pre>'; die();
    }

    /**
     * @param $model_date
     * @return array
     */
    protected function getProgramm($model_date)
    {
        $programmList = $model_date->programm_id ?: [];
        if (!$model_date->programm_id && $this->education_cat_id) {
            $programmList = EducationProgramm::getProgrammListByName($this->education_cat_id);
            $programmList = array_keys($programmList);
        }
        return $programmList;
    }

    /**
     * Запрос на нагрузку преподавателя/конц-ра
     * @param $teachersIds
     * @return array
     */
    public function getTeachersLoadStudyplan()
    {
        $array = TeachersLoadStudyplanView::find()
            ->select(new \yii\db\Expression('week_time,year_time_consult,subject_sect_studyplan_id,studyplan_id,student_id,student_fio,plan_year,course,teachers_load_id,direction_id,direction_vid_id,teachers_id,load_time,load_time_consult,subject'))
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['status' => 1])
            ->asArray()
            ->all();

        $data = ArrayHelper::index($array, null, ['studyplan_id']);
        return $data;
    }

    protected function getStudyplanModels()
    {
        $models = (new \yii\db\Query())->from('studyplan_view')
            ->select('id, education_programm_name, student_fio')
            ->where(['status' => Studyplan::STATUS_ACTIVE])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andFilterWhere(['programm_id' => $this->programm_id])
            ->all();
        return $models;
    }

    public function getStudyplan()
    {
        return ArrayHelper::map($this->getStudyplanModels(), 'id', 'student_fio');
    }

    public function getDataTeachersStudyplan()
    {
        $load_data = [];

        $attributes = ['student_fio' => 'Ученики/Индивидуальные планы'];
        $attributes += ['education_programm_name' => 'Программа'];
        $attributes += ['scale' => 'Монитор заполнения нагрузки'];

        $models = ArrayHelper::index($this->getStudyplanModels(), 'id');
//        echo '<pre>' . print_r($models, true) . '</pre>';        die();
        foreach ($models as $studyplan_id => $value) {
            $dataTeachers = $this->teachersLoad[$studyplan_id] ?? [];
            $flag = false;
            $load_data_temp[$studyplan_id]['scale'] = '';
            foreach ($dataTeachers as $item => $dataSect) {
                $check = $this->getCheckLabel($dataSect, $flag);
                $load_data_temp[$studyplan_id]['scale'] .= $check;
            }
            if (!$flag && $this->bad_flag) {
                continue;
            }
            $load_data[$studyplan_id]['scale'] = $load_data_temp[$studyplan_id]['scale'];
            $load_data[$studyplan_id]['studyplan_id'] = $studyplan_id;
            $load_data[$studyplan_id]['student_fio'] = $value['student_fio'];
            $load_data[$studyplan_id]['education_programm_name'] = $value['education_programm_name'];
        }
//        echo '<pre>' . print_r($load_data, true) . '</pre>'; die();
        return ['data' => $load_data, 'attributes' => $attributes];
    }

    /**
     * @param $value
     * @param $flag
     * @return string
     */
    protected function getCheckLabel($value, &$flag)
    {
        $check = '<i class="fa fa-square-o" aria-hidden="true" style="color: grey"></i>';

        if ($value['load_time'] == 0 and $value['week_time'] != 0) {
            $check = '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>';
            $flag = true;
        } elseif ($value['load_time'] == $value['week_time'] && $value['direction_id'] == 1000) {
            $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
        } elseif ($value['load_time'] != 0 && $value['direction_id'] != 1000) {
            $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
        } elseif ($value['load_time'] != $value['week_time']) {
            $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: orange"></i>';
            $flag = true;
        }
        $check = Html::a($check, '#', ['title' => $value['subject']]);

        return $check;

    }
    public static function getCheckLabelHints()
    {
        $check[] = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i> - Заполнено';
        $check[] = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: darkorange"></i> - Не соответствие нагрузке';
        $check[] = '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i> - Не заполнено';

        return implode('<br/>', $check);

    }

}