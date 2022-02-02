<?php

namespace common\models\schedule;

use artsoft\helpers\ArtHelper;
use common\models\auditory\Auditory;
use common\models\guidejob\Direction;
use common\models\teachers\Teachers;
use Yii;

/**
 * This is the model class for table "consult_schedule_teachers_view".
 *
 * @property int|null $teachers_load_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property string|null $studyplan_subject_list
 * @property int|null $course
 * @property int|null $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property int|null $subject_vid_id
 * @property float|null $year_time_consult
 * @property int|null $plan_year
 * @property int|null $consult_schedule_id
 * @property int|null $datetime_in
 * @property int|null $datetime_out
 * @property int|null $auditory_id
 * @property string|null $description
 */
class ConsultScheduleTeachersView extends ConsultSchedule
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consult_schedule_teachers_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Studyplan Subject ID'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan Subject List'),
            'course' => Yii::t('art/guide', 'Course'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'year_time_consult' => Yii::t('art/guide', 'Year Time Consult'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'consult_schedule_id' => Yii::t('art/guide', 'Consult Schedule ID'),
            'datetime_in' => Yii::t('art/guide', 'Time In'),
            'datetime_out' => Yii::t('art/guide', 'Time Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory'),
            'description' => Yii::t('art', 'Description'),
        ];
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'teachers_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(Auditory::class, ['id' => 'auditory_id']);
    }

    public function getTeachersConsultNeed()
    {
        return true;
    }

}
