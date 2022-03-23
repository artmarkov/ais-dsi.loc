<?php
namespace common\models\history;

use common\models\education\LessonProgress;
use common\widgets\history\BaseHistory;

class LessonItemsProgressHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'lesson_progress_hist';
    }

    public static function getModelName()
    {
        return LessonProgress::class;
    }

    protected function getFields()
    {
        return [
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
            case 'lesson_mark_id':
                return isset($model->lesson_mark_id) ? $model->lessonMark->mark_label . ' [' . LessonProgress::getStudentName($model->studyplan_subject_id) . ']'  : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

}