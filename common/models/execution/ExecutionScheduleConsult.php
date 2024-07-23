<?php

namespace common\models\execution;

use artsoft\widgets\Notice;
use common\models\schedule\ConsultScheduleConfirm;
use common\models\schedule\ConsultScheduleView;
use common\models\studyplan\Studyplan;
use yii\helpers\ArrayHelper;
use Yii;
use artsoft\widgets\Tooltip;
use yii\helpers\Html;

/**
 * Class ExecutionScheduleConsult
 * @package common\models\execution
 *
 */
class ExecutionScheduleConsult
{
    protected $plan_year;
    protected $teachers_id;
    protected $teachersIds;
    protected $teachersSchedule;
    protected $teachersScheduleConfirm;

    public static function getData($model_date)
    {
        return new self($model_date);
    }

    public function __construct($model_date)
    {
        $this->teachers_id = $model_date->teachers_id;
        $this->plan_year = $model_date->plan_year;
        $this->teachersIds = !$model_date->teachers_id ? array_keys(\artsoft\helpers\RefBook::find('teachers_fio', 1)->getList()) : [$model_date->teachers_id];
        $this->teachersScheduleConfirm = $this->getTeachersScheduleConfirm();
//        echo '<pre>' . print_r($this->teachersScheduleConfirm, true) . '</pre>';
        $this->teachersSchedule = $this->getTeachersSchedule();

    }

    protected function getTeachersScheduleConfirm()
    {
        $models = ConsultScheduleConfirm::find()->select('id,teachers_id,confirm_status,teachers_sign')->where(['teachers_id' => $this->teachersIds])->andWhere(['=', 'plan_year', $this->plan_year])->asArray()->all();
        return ArrayHelper::index($models, 'teachers_id');
    }

    /**
     * Запрос на полное время занятий расписания преподавателя данной нагрузки
     * @param $teachersLoadIds
     * @return array
     */
    public function getTeachersSchedule()
    {
        $array = ConsultScheduleView::find()
            ->select('teachers_id,subject_sect_studyplan_id,studyplan_subject_id,subject,sect_name,load_time_consult, SUM((datetime_out-datetime_in)/2700) as load_time_summ')
            ->where(['teachers_id' => $this->teachersIds])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ])
            ->groupBy('teachers_id,subject_sect_studyplan_id,studyplan_subject_id,subject,sect_name,load_time_consult')
            ->asArray()
            ->all();
        return ArrayHelper::index($array, null, ['teachers_id', 'subject_sect_studyplan_id', 'studyplan_subject_id']);

    }

    public function getDataTeachers()
    {
        $load_data = [];

        $attributes = ['teachers_id' => 'Преподаватели'];
        $attributes += ['scale_0' => 'Групповые/Мелкогрупповые'];
        $attributes += ['scale_1' => 'Индивидуальные'];
        $attributes += ['confirm_status' => 'Статус'];
        $attributes += ['teachers_sign' => 'Подписант'];

        foreach ($this->teachersIds as $i => $teachers_id) {
            $dataTeachers = $this->teachersSchedule[$teachers_id] ?? [];
            $load_data[$teachers_id]['teachers_id'] = $teachers_id;
            $check = '';
            $load_data[$teachers_id]['scale_0'] = '';
            $load_data[$teachers_id]['scale_1'] = '';
            foreach ($dataTeachers as $subject_sect_studyplan_id => $dataSect) {
                foreach ($dataSect as $studyplan_subject_id => $dataSubject) {
                    foreach ($dataSubject as $item => $value) {
                        $check = $value['load_time_summ'] == 0 ? '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>' : ($value['load_time_consult'] == $value['load_time_summ'] ? '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>' : '<i class="fa fa-check-square-o" aria-hidden="true" style="color: darkorange"></i>');
                        if ($subject_sect_studyplan_id == 0) {
                          //  $check = Html::a($check, ['/studyplan/default/consult-items', 'id' => $value['studyplan_id']], ['target' => '_blank', 'title' => $value['subject'] . '-' . $value['sect_name']]);;
                            $load_data[$teachers_id]['scale_1'] .= $check;
                        } else {
                          //  $check = Html::a($check, ['/sect/default/consult-items', 'id' => $value['subject_sect_id']], ['target' => '_blank', 'title' => $value['subject'] . '-' . $value['sect_name']]);;
                            $load_data[$teachers_id]['scale_0'] .= $check;
                        }
                    }
                }
            }
            $load_data[$teachers_id]['confirm_status'] = isset($this->teachersScheduleConfirm[$teachers_id]) ? $this->teachersScheduleConfirm[$teachers_id]['confirm_status'] : null;
            $load_data[$teachers_id]['teachers_sign'] = isset($this->teachersScheduleConfirm[$teachers_id]) ? $this->teachersScheduleConfirm[$teachers_id]['teachers_sign'] : null;
        }
//        echo '<pre>' . print_r($this->teachersSchedule, true) . '</pre>';
        return ['data' => $load_data, 'attributes' => $attributes];
    }

    public static function getStatusLabel($status)
    {
        switch ($status) {
            case ConsultScheduleConfirm::DOC_STATUS_DRAFT:
                $label = '<span class="label label-primary">' . Yii::t('art', 'Draft') . '</span>';
                break;
            case ConsultScheduleConfirm::DOC_STATUS_AGREED:
                $label = '<span class="label label-success">' . Yii::t('art', 'Agreed') . '</span>';
                break;
            case ConsultScheduleConfirm::DOC_STATUS_WAIT:
                $label = '<span class="label label-warning">' . Yii::t('art', 'Wait') . '</span>';
                break;
            case ConsultScheduleConfirm::DOC_STATUS_MODIF:
                $label = '<span class="label label-warning">' . Yii::t('art', 'Modif') . '</span>';
                break;
            default:
                $label = '';
        }
        return $label;

    }
}