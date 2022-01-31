<?php

namespace common\models\subjectsect;

class SubjectScheduleTeachersView extends SubjectScheduleView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_schedule_teachers_view';
    }
}
