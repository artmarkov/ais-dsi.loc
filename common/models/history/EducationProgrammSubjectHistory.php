<?php

namespace common\models\history;

use common\models\education\EducationProgrammSubject;
use common\widgets\history\BaseHistory;

class EducationProgrammSubjectHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'education_programm_subject_hist';
    }

    public static function getModelName()
    {
        return EducationProgrammSubject::class;
    }

    protected function getFields()
    {
        return [
            'subject_cat_id',
            'subject_id',
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
        }
        return parent::getDisplayValue($model, $name, $value);
    }
    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        foreach (EducationProgrammSubjectTimeHistory::getLinkedIdList('programm_subject_id', $this->objId) as $timeId) {
            $vf = new EducationProgrammSubjectTimeHistory($timeId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}