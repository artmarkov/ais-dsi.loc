<?php

namespace common\models\execution;

use artsoft\helpers\ArtHelper;
use artsoft\widgets\Notice;
use common\models\schedule\ConsultScheduleConfirm;
use common\models\schedule\ConsultScheduleView;
use common\models\studyplan\Studyplan;
use common\models\teachers\PortfolioView;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Yii;
use artsoft\widgets\Tooltip;
use yii\helpers\Html;

/**
 * Class ExecutionSchoolplanPerform
 * @package common\models\execution
 *
 */
class ExecutionSchoolplanPerform
{
    protected $plan_year;
    protected $timestamp_in;
    protected $timestamp_out;
    protected $teachers_id;
    protected $teachersIds;
    protected $teachersPerform;

    public static function getData($model_date)
    {
        return new self($model_date);
    }

    public function __construct($model_date)
    {
        $this->teachers_id = $model_date->teachers_id;
        $this->plan_year = $model_date->plan_year;
        $this->getDateParams();
        $this->teachersIds = !$model_date->teachers_id ? array_keys(\artsoft\helpers\RefBook::find('teachers_fio', 1)->getList()) : [$model_date->teachers_id];
//        echo '<pre>' . print_r($this->teachersScheduleConfirm, true) . '</pre>';
        $this->teachersPerform = $this->getTeachersPerform();

    }

    protected function getDateParams()
    {
        $data = ArtHelper::getStudyYearParams($this->plan_year);
        $this->timestamp_in = $data['timestamp_in'];
        $this->timestamp_out = $data['timestamp_out'];
    }

    /**
     * Запрос на полное время занятий расписания преподавателя данной нагрузки
     * @param $teachersLoadIds
     * @return array
     */
    public function getTeachersPerform()
    {
        $array = PortfolioView::find()
            ->select('schoolplan_id,schoolplan_perform_id,title,datetime_in,datetime_out,studyplan_id,studyplan_subject_id,teachers_id,status_exe,status_sign,signer_id,sect_name,subject')
            ->where(['teachers_id' => $this->teachersIds])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->asArray()
            ->all();
        return ArrayHelper::index($array, null, ['teachers_id']);

    }

    public function getDataTeachers()
    {
        $load_data = [];

        $attributes = ['teachers_id' => 'Преподаватель'];
        $attributes += ['scale' => 'Шкала выполнения'];

//        echo '<pre>' . print_r($this->teachersPerform, true) . '</pre>'; die();
        foreach ($this->teachersIds as $i => $teachers_id) {
            $dataTeachers = $this->teachersPerform[$teachers_id] ?? [];
            $load_data[$teachers_id]['teachers_id'] = $teachers_id;
            $load_data[$teachers_id]['scale'] = '';
            foreach ($dataTeachers as $item => $value) {
                $check = $this->getCheckLabel($value);
                $text = Yii::$app->formatter->asDatetime($value['datetime_in']) . ' - ' . Yii::$app->formatter->asDatetime($value['datetime_out']) . ' Мероприятие: ' . $value['title'] . ' Участник:' . $value['sect_name'] . ' Дисциплина: ' . $value['subject'];
                $check = Html::a($check, ['/schoolplan/default/perform', 'id' => $value['schoolplan_id'], 'objectId' => $value['schoolplan_perform_id'], 'mode' => 'view'], ['target' => '_blank', 'title' => $text]);;
                $load_data[$teachers_id]['scale'] .= $check;
            }
        }
        return ['data' => $load_data, 'attributes' => $attributes];
    }

    protected function getCheckLabel($value)
    {
//        status_exe
//            [1, 'В работе', 'info'],
//            [2, 'Выполнено', 'success'],
//            [3, 'Не выполнено', 'danger']
//        status_sign
//            0 => Yii::t('art', 'Draft'),
//            1 => Yii::t('art', 'Agreed'),
//            2 => Yii::t('art', 'Wait'),
        if(Yii::$app->settings->get('mailing.schoolplan_perform_doc')) {
            if ($value['status_sign'] == 0 && $value['status_exe'] == 2) {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: gray"></i>';
            } elseif ($value['status_sign'] == 1 && $value['status_exe'] == 2) {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
            } elseif ($value['status_sign'] == 2 && $value['status_exe'] == 2) {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: darkorange"></i>';
            } else {
                $check = '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>';
            }
        } else {
            if ($value['status_exe'] == 1) {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: gray"></i>';
            } elseif ($value['status_exe'] == 2) {
                $check = '<i class="fa fa-check-square-o" aria-hidden="true" style="color: green"></i>';
            } elseif ($value['status_exe'] == 3) {
                $check = '<i class="fa fa-square-o" aria-hidden="true" style="color: red"></i>';
            }
        }
        return $check;

    }
}