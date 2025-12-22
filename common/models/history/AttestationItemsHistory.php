<?php

namespace common\models\history;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\models\education\AttestationItems;
use common\widgets\history\BaseHistory;

class AttestationItemsHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'attestation_items_hist';
    }

    public static function getModelName()
    {
        return AttestationItems::class;
    }

    protected function getFields()
    {
        return [
            'plan_year',
            'studyplan_subject_id',
            'lesson_mark_id',
            'mark_rem',
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
            case 'plan_year':
                return isset($model->plan_year) ? ArtHelper::getStudyYearsList()[$value] : $value;
                break;
            case 'studyplan_subject_id':
                return isset($model->studyplan_subject_id) ? RefBook::find('subject_memo_4')->getValue($model->studyplan_subject_id) : $value;
                break;
            case 'lesson_mark_id':
                return isset($model->lesson_mark_id) ? $model->lessonMark->mark_label : $value;
                break;

        }
        return parent::getDisplayValue($model, $name, $value);
    }

}