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

    public static function getScheduleIsExist($subject_sect_studyplan_id, $studyplan_subject_id)
    {
        $studyplan_subject_id = $subject_sect_studyplan_id == 0 ? $studyplan_subject_id : 0;

        $scheduleIsExist = self::find()->where(
            ['AND',
                ['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id],
                ['=', 'studyplan_subject_id', $studyplan_subject_id],
            ]);
        return $scheduleIsExist->exists();
    }
}
