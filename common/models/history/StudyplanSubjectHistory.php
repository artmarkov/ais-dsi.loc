<?php

namespace common\models\history;

use common\models\studyplan\StudyplanSubject;
use common\widgets\history\BaseHistory;

class StudyplanSubjectHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'studyplan_subject_hist';
    }

    public static function getModelName()
    {
        return StudyplanSubject::class;
    }

    protected function getFields()
    {
        return [
            'subject_cat_id',
            'subject_id',
            'subject_type_id',
            'week_time',
            'year_time',
            'cost_hour',
            'cost_month_summ',
            'cost_year_summ',
            'year_time_consult',
            'med_cert',
            'fin_cert',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return null
     */
    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'subject_cat_id':
                return isset($model->subject_cat_id) ? $model->subjectCat->name : $value;
            case 'subject_id':
                return isset($model->subject_id) ? $model->subject->name : $value;
            case 'subject_type_id':
                return isset($model->subject_type_id) ? $model->subjectType->name : $value;
            case 'med_cert':
                return isset($model->med_cert) ? ($model->med_cert ? 'Yes' : 'No') : $value;
            case 'fin_cert':
                return isset($model->fin_cert) ? ($model->fin_cert ? 'Yes' : 'No') : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
}