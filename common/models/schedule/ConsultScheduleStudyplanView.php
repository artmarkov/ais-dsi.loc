<?php

namespace common\models\schedule;

use common\models\guidejob\Direction;
use Yii;

/**
 * This is the model class for table "consult_schedule_studyplan_view".
 *
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_sect_studyplan_id
 * @property float|null $year_time_consult
 * @property string|null $studyplan_subject_list
 * @property int|null $subject_sect_id
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property int|null $plan_year
 * @property int|null $status
 * @property int|null $teachers_load_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property int|null $consult_schedule_id
 * @property int|null $datetime_in
 * @property int|null $datetime_out
 * @property int|null $auditory_id
 * @property string|null $description
 */
class ConsultScheduleStudyplanView extends ConsultSchedule
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
        return [
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'load_time_consult' => Yii::t('art/guide', 'Load Time Consult'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/guide', 'Student ID'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'status' => Yii::t('art/guide', 'Status'),
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
    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    public function getTeachersConsultNeed() {
        return true;
    }
}
