<?php

namespace common\models\schedule;

class SubjectScheduleView extends SubjectScheduleStudyplanView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_schedule_view';
    }

}
