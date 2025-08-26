<?php

namespace common\models\execution;

use common\models\teachers\TeachersLoadView;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class ExecutionLoad
 * @package common\models\execution
 *
 */
class ExecutionLoad
{
    protected $plan_year;
    protected $teachers_id;
    protected $teachersIds;
    protected $teachersLoad;

    public static function getData($model_date)
    {
        return new self($model_date);
    }

    public function __construct($model_date)
    {
        $this->teachers_id = $model_date->teachers_id;
        $this->plan_year = $model_date->plan_year;
        $this->teachersIds = !$model_date->teachers_id ? array_keys(\artsoft\helpers\RefBook::find('teachers_fio', 1)->getList()) : [$model_date->teachers_id];
        $this->teachersLoad = $this->getTeachersLoad();
//        echo '<pre>' . print_r($this->teachersLoad, true) . '</pre>';

    }


    /**
     * Запрос на нагрузку преподавателя/конц-ра
     * @param $teachersIds
     * @return array
     */
    public function getTeachersLoad()
    {
        $array = TeachersLoadView::find()
            ->select(new \yii\db\Expression('teachers_id,direction_id,subject_sect_studyplan_id,studyplan_subject_id,subject_sect_id,subject,sect_name,week_time,year_time_consult,load_time,load_time_consult'))
            ->where(['teachers_id' => $this->teachersIds])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['status' => 1])
            ->asArray()
            ->all();

        $data = ArrayHelper::index($array, null, ['teachers_id']);
        return $data;
    }

    public function getDataTeachers()
    {
        $load_data = [];

        $attributes = ['teachers_id' => 'Преподаватели'];
        $attributes += ['scale' => 'Монитор заполнения нагрузки'];


        foreach ($this->teachersIds as $i => $teachers_id) {
            $dataTeachers = $this->teachersLoad[$teachers_id] ?? [];
            $load_data[$teachers_id]['teachers_id'] = $teachers_id;
            $load_data[$teachers_id]['scale'] = '';

            foreach ($dataTeachers as $item => $dataSect) {

                $check = $this->getCheckLabel($dataSect);
                $load_data[$teachers_id]['scale'] .= $check;
            }
        }
//        echo '<pre>' . print_r($load_data, true) . '</pre>';
        return ['data' => $load_data, 'attributes' => $attributes];
    }

    protected function getCheckLabel($value)
    {
        $check = '<i class="fa fa-square-o" aria-hidden="true" style="color: grey"></i>';

        if ($value['load_time'] == 0 and $value['week_time'] != 0) {
            $check = '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>';
        } elseif ($value['load_time'] == $value['week_time'] && $value['direction_id'] == 1000) {
            $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
        } elseif ($value['load_time'] != 0 && $value['direction_id'] != 1000) {
            $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
        } elseif ($value['load_time'] != $value['week_time']) {
            $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: orange"></i>';
        }
        $check = Html::a($check, '#', ['title' => $value['subject'] . ' - ' . $value['sect_name']]);

        return $check;

    }


}