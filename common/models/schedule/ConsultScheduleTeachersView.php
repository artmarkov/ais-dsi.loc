<?php

namespace common\models\schedule;

class ConsultScheduleTeachersView extends ConsultScheduleView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consult_schedule_teachers_view';
    }
}
