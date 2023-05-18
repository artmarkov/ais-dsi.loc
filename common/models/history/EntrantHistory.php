<?php

namespace common\models\history;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\models\entrant\Entrant;
use common\models\subject\SubjectType;
use common\widgets\history\BaseHistory;
use yii\helpers\Json;

class EntrantHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'entrant_hist';
    }

    public static function getModelName()
    {
        return Entrant::class;
    }

    protected function getFields()
    {
        return [
            'student_id',
            'group_id',
            'subject_list',
            'last_experience',
            'remark',
            'decision_id',
            'reason',
            'programm_id',
            'course',
            'type_id',
            'status',
        ];
    }

    protected static function getDisplayValue($model, $name, $value)
    {
        switch ($name) {
            case 'student_id':
                return isset($model->student_id) ? $model->student->fullName : $value;
            case 'group_id':
                return isset($model->group_id) ? $model->group->name : $value;
            case 'decision_id':
                return isset($model->decision_id) ? Entrant::getDecisionValue($model->decision_id) : $value;
            case 'programm_id':
                return isset($model->programm_id) ? RefBook::find('education_programm_name')->getValue($model->programm_id) : $value;
            case 'course':
                return isset($model->course) ? ArtHelper::getCourseList()[$model->course] : $value;
            case 'type_id':
                return isset($model->type_id) ? SubjectType::findOne($model->type_id)->name : $value;
            case 'subject_list':
                if (isset($model->subject_list)) {
                    $v = [];
                    foreach (Json::decode($model->subject_list) as $id) {
                        $v[] = $id != null ? RefBook::find('subject_name')->getValue($id) : null;
                    }
                    return implode(', ', $v);
                }
            case 'status':
                return isset($model->status) ? Entrant::getStatusValue($value) : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        foreach (EntrantMembersHistory::getLinkedIdList('entrant_id', $this->objId) as $itemId) {
            $vf = new EntrantMembersHistory($itemId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}