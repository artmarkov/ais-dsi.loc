<?php

namespace common\models\execution;

use artsoft\helpers\ArtHelper;
use artsoft\widgets\Notice;
use common\models\schedule\ConsultScheduleConfirm;
use common\models\schedule\ConsultScheduleView;
use common\models\studyplan\Studyplan;
use common\models\studyplan\ThematicView;
use common\models\teachers\TeachersLoadView;
use yii\helpers\ArrayHelper;
use Yii;
use artsoft\widgets\Tooltip;
use yii\helpers\Html;

/**
 * Class ExecutionThematic
 * @package common\models\execution
 *
 */
class ExecutionThematic
{
    protected $plan_year;
    protected $teachers_id;
    protected $teachersIds;
    protected $teachersThemaric;

    public static function getData($model_date)
    {
        return new self($model_date);
    }

    public function __construct($model_date)
    {
        $this->teachers_id = $model_date->teachers_id;
        $this->plan_year = $model_date->plan_year;
        $this->teachersIds = !$model_date->teachers_id ? array_keys(\artsoft\helpers\RefBook::find('teachers_fio', 1)->getList()) : [$model_date->teachers_id];
        $this->teachersThemaric = $this->getTeachersThemaric();
//        echo '<pre>' . print_r($this->teachersThemaric, true) . '</pre>';

    }


    /**
     * Запрос на тематические планы преподавателя
     * @param $teachersIds
     * @return array
     */
    public function getTeachersThemaric()
    {
        $array = TeachersLoadView::find()
            ->select(new \yii\db\Expression('teachers_id,subject_sect_studyplan_id,studyplan_subject_id,subject_sect_id,subject,sect_name, 0 as half_year'))
            ->where(['teachers_id' => $this->teachersIds])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['status' => Studyplan::STATUS_ACTIVE])
            ->asArray()
            ->all();
        $array1 = ThematicView::find()
            ->select(new \yii\db\Expression('teachers_id,subject_sect_studyplan_id,studyplan_subject_id,subject_sect_id,subject,sect_name,
                            studyplan_thematic_id,thematic_category,author_id,doc_status,doc_sign_teachers_id,doc_sign_timestamp,half_year'))
            ->where(['teachers_id' => $this->teachersIds])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['status' => Studyplan::STATUS_ACTIVE])
            ->andWhere(['IS NOT', 'half_year', NULL])
            ->asArray()
            ->all();
        $data = ArrayHelper::index(array_merge($array, $array1), null, ['teachers_id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'half_year']);
        return $data;
//        echo '<pre>' . print_r($data, true) . '</pre>';        die();
    }

    public function getDataTeachers()
    {
        $load_data = [];

        $attributes = ['teachers_id' => 'Преподаватели'];
        $attributes += ['scale_0' => 'Групповые/Мелкогрупповые'];
        $attributes += ['scale_1' => 'Индивидуальные'];
        $attributes += ['scale_2' => 'Групповые/Мелкогрупповые'];
        $attributes += ['scale_3' => 'Индивидуальные'];

        foreach ($this->teachersIds as $i => $teachers_id) {
            $dataTeachers = $this->teachersThemaric[$teachers_id] ?? [];
            $load_data[$teachers_id]['teachers_id'] = $teachers_id;
            $load_data[$teachers_id]['scale_0'] = '';
            $load_data[$teachers_id]['scale_1'] = '';
            $load_data[$teachers_id]['scale_2'] = '';
            $load_data[$teachers_id]['scale_3'] = '';
            foreach ($dataTeachers as $subject_sect_studyplan_id => $dataSect) {
                foreach ($dataSect as $studyplan_subject_id => $dataSubject) {
                    foreach (ArtHelper::getHalfYearList(true) as $half_year => $half_year_name) {
                        if (isset($dataSubject[$half_year])) {
                            $halfArray = $dataSubject[$half_year];
                        } else {
                            $halfArray = $dataSubject[0];
                        }
                        foreach ($halfArray as $item => $value) {
                            $check = $this->getCheckLabel($value);
                            if ($half_year == 1) {
                                if ($subject_sect_studyplan_id == 0) {
                                    $load_data[$teachers_id]['scale_1'] .= $check;
                                } else {
                                    $load_data[$teachers_id]['scale_0'] .= $check;
                                }
                            } else {
                                if ($subject_sect_studyplan_id == 0) {
                                    $load_data[$teachers_id]['scale_3'] .= $check;
                                } else {
                                    $load_data[$teachers_id]['scale_2'] .= $check;
                                }
                            }
                        }
                    }
                }
            }
        }
//        echo '<pre>' . print_r($load_data, true) . '</pre>';
        return ['data' => $load_data, 'attributes' => $attributes];
    }

    protected function getCheckLabel($value)
    {
        if (isset($value['studyplan_thematic_id'])) {

            if ($value['doc_status'] == 1) {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
            } elseif ($value['doc_status'] == 2) {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: darkorange"></i>';
            } else {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: grey"></i>';
            }
        } else {
            $check = '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>';
        }
        return $check;

    }


}