<?php

namespace common\models\history;

use common\models\education\EducationProgrammSubjectTime;
use common\widgets\history\BaseHistory;

class EducationProgrammSubjectTimeHistory extends BaseHistory
{
    public static function getTableName()
    {
        return 'education_programm_subject_time_hist';
    }

    public static function getModelName()
    {
        return EducationProgrammSubjectTime::class;
    }

    protected function getFields()
    {
        return [
            'cource',
            'week_time',
            'year_time',
        ];
    }
}