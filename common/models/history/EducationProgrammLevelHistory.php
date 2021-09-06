<?php

namespace common\models\history;

use common\models\education\EducationProgrammLevel;
use common\widgets\history\BaseHistory;

class EducationProgrammLevelHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'education_programm_level_hist';
    }

    public static function getModelName()
    {
        return EducationProgrammLevel::class;
    }

    protected function getFields()
    {
        return [
            'course',
            'level_id',
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
            case 'level_id':
                return isset($model->level_id) ? $model->level->name : $value;
        }
        return parent::getDisplayValue($model, $name, $value);
    }
    /**
     * @return array
     */
    public function getHistory()
    {
        $selfHistory = parent::getHistory();

        foreach (EducationProgrammLevelSubjectHistory::getLinkedIdList('programm_level_id', $this->objId) as $timeId) {
            $vf = new EducationProgrammLevelSubjectHistory($timeId);
            $selfHistory = array_merge($selfHistory, $vf->getHistory());
        }

        krsort($selfHistory);
        return $selfHistory;
    }
}