<?php

namespace common\models\schedule;

use common\models\guidejob\Direction;
use Yii;

/**
 * This is the model class for table "consult_schedule_view".
 *
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property int|null $plan_year
 * @property int|null $programm_id
 * @property int|null $speciality_id
 * @property string|null $studyplan_subject_list
 * @property int|null $course
 * @property int|null $status
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property int|null $subject_vid_id
 * @property float|null $year_time_consult
 * @property int|null $teachers_load_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property int|null $consult_schedule_id
 * @property int|null $datetime_in
 * @property int|null $datetime_out
 * @property int|null $auditory_id
 * @property string|null $description
 */
class ConsultScheduleView extends ConsultSchedule
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consult_schedule_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/guide', 'Student ID'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'speciality_id' => Yii::t('art/studyplan', 'Speciality Name'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
            'status' => Yii::t('art/guide', 'Status'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'year_time_consult' => Yii::t('art/guide', 'Year Time Consult'),
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
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
