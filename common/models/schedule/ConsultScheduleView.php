<?php

namespace common\models\schedule;

use Yii;

class ConsultScheduleView extends ConsultScheduleStudyplanView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consult_schedule_view';
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'load_time_consult' => Yii::t('art/guide', 'Load Time Consult'),
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'consult_schedule_id' => Yii::t('art/guide', 'Consult Schedule ID'),
            'datetime_in' => Yii::t('art/guide', 'Datetime In'),
            'datetime_out' => Yii::t('art/guide', 'Datetime Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory ID'),
            'description' => Yii::t('art/guide', 'Description'),
        ];
    }
}
