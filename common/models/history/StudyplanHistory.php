<?php

namespace common\models\history;


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
            'course',
            'plan_year',
            'description',
            'status',
        ];
    }

    /**
     * @param $model
     * @param $name
     * @param $value
     * @return string|null
     * @throws \yii\base\InvalidConfigException
     */
//    protected static function getDisplayValue($model, $name, $value)
//    {
//        switch ($name) {
//            case 'position_id':
//                return isset($model->position_id) ? $model->position->name : $value;
//            case 'sert_name':
//                return isset(self::getModelName()::STUDENT_DOC[$value]) ? self::getModelName()::STUDENT_DOC[$value] : $value;
//        }
//        return parent::getDisplayValue($model, $name, $value);
//    }
//
//    /**
//     * @return array
//     */
//    public function getHistory()
//    {
//        $selfHistory = parent::getHistory();
//
//        $id = $this->getModelName()::findOne($this->objId)->user->id;
//        $vf = new UserCommonHistory($id);
//        $selfHistory = array_merge($selfHistory, $vf->getHistory());
//
//        foreach (StudentDependenceHistory::getLinkedIdList('student_id', $this->objId) as $studentId) {
//            $vf = new StudentDependenceHistory($studentId);
//            $selfHistory = array_merge($selfHistory, $vf->getHistory());
//        }
//
//        krsort($selfHistory);
//        return $selfHistory;
//    }
}