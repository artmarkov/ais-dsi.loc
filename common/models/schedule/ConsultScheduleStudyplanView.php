<?php

namespace common\models\schedule;

use Yii;

/**
 * This is the model class for table "consult_schedule_studyplan_view".
 *
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_list
 * @property string|null $subject_type_id
 * @property int|null $subject_sect_id
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property int|null $plan_year
 * @property int|null $status
 * @property int|null $teachers_load_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property float|null $load_time_consult
 * @property int|null $consult_schedule_id
 * @property int|null $datetime_in
 * @property int|null $datetime_out
 * @property string|null $auditory_id
 * @property string|null $description
 */

class ConsultScheduleStudyplanView extends ConsultScheduleView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consult_schedule_studyplan_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['studyplan_id'] = Yii::t('art/guide', 'Studyplan');
        $attr['student_id'] = Yii::t('art/student', 'Student');
        $attr['student_fio'] = Yii::t('art/student', 'Student');
        return $attr;
    }

}
