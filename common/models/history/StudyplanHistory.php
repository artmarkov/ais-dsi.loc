<?php

namespace common\models\history;


use artsoft\helpers\ArtHelper;
use common\models\studyplan\Studyplan;
use common\widgets\history\BaseHistory;

class StudyplanHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'studyplan_hist';
    }

    public static function getModelName()
    {
        return Studyplan::class;
    }

    protected function getFields()
    {
        return [
            'student_id',
            'programm_id',
            'subject_form_id',
            'course',
            'plan_year',
            'description',
            'status',
            'status_reason',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return string|null
     * @throws \yii\base\InvalidConfigException
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'student_id':
                return isset($model->student) ? $model->student->fullName : $value;
            case 'programm_id':
                return isset($model->programm_id) ? $model->programm->name : $value;
            case 'subject_form_id':
                return isset($model->subject_form_id) ? $model->subjectForm->name : $value;
            case 'status':
                return isset($model->status) ? Studyplan::getStatusList()[$value] : $value;
            case 'status_reason':
                return isset($model->status_reason) ? Studyplan::getStatusReasonList()[$value] : $value;
            case 'plan_year':
                return isset($model->plan_year) ? ArtHelper::getStudyYearsList()[$value] : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        foreach (StudyplanSubjectHistory::getLinkedIdList('studyplan_id', $this->objId) as $studyplanId) {
            $vf = new StudyplanSubjectHistory($studyplanId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}