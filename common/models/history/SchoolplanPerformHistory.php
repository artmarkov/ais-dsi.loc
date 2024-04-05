<?php

namespace common\models\history;

use artsoft\helpers\RefBook;
use common\models\education\LessonProgress;
use common\models\schoolplan\SchoolplanPerform;
use common\models\studyplan\StudyplanThematicItems;
use common\widgets\history\BaseHistory;
use Yii;
use yii\helpers\Json;

class SchoolplanPerformHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'schoolplan_perform_hist';
    }

    public static function getModelName()
    {
        return SchoolplanPerform::class;
    }

    protected function getFields()
    {
        return [
            'schoolplan_id',
            'studyplan_subject_id',
            'teachers_id',
            'thematic_items_list',
            'lesson_mark_id',
            'winner_id',
            'resume',
            'status_exe',
            'status_sign',
            'signer_id',
        ];
    }

    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'schoolplan_id':
                return isset($model->schoolplan) ? $model->schoolplan->title : $value;
            case 'studyplan_subject_id':
                return isset($model->studyplan_subject_id) ? RefBook::find('subject_memo_4')->getValue($model->studyplan_subject_id) : $value;
            case 'teachers_id':
                return isset($model->teachers_id) ? RefBook::find('teachers_fio')->getValue($model->teachers_id) : $value;
                break;
            case 'lesson_mark_id':
                return isset($model->lesson_mark_id) ? $model->lessonMark->mark_label . ' [' . LessonProgress::getStudentName($model->studyplan_subject_id) . ']' : $value;
            case 'thematic_items_list':
                if (isset($model->thematic_items_list)) {
                    $v = [];
                    foreach (Json::decode($model->thematic_items_list) as $id) {
                        $v[] = $id != null ? (StudyplanThematicItems::findOne(['id' => $id]) ? StudyplanThematicItems::findOne(['id' => $id])->topic : $id) : null;
                    }
                    return implode(', ', $v);
                }
            case 'status_exe':
                return isset($model->status_exe) ? $model->getStatusExeValue($value) : $value;
            case 'winner_id':
                return isset($model->winner_id) ? $model->getWinnerValue($value) : $value;

        }
        return parent::getDisplayValue($model, $name, $value);
    }

}