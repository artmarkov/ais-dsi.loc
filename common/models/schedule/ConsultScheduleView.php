<?php

namespace common\models\schedule;

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
class ConsultScheduleView extends \yii\db\ActiveRecord
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
    public function rules()
    {
        return [
            [['studyplan_id', 'student_id', 'plan_year', 'programm_id', 'speciality_id', 'course', 'status', 'studyplan_subject_id', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'teachers_load_id', 'subject_sect_studyplan_id', 'direction_id', 'teachers_id', 'consult_schedule_id', 'datetime_in', 'datetime_out', 'auditory_id'], 'default', 'value' => null],
            [['studyplan_id', 'student_id', 'plan_year', 'programm_id', 'speciality_id', 'course', 'status', 'studyplan_subject_id', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'teachers_load_id', 'subject_sect_studyplan_id', 'direction_id', 'teachers_id', 'consult_schedule_id', 'datetime_in', 'datetime_out', 'auditory_id'], 'integer'],
            [['studyplan_subject_list'], 'string'],
            [['year_time_consult'], 'number'],
            [['description'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'studyplan_id' => Yii::t('art/guide', 'Studyplan ID'),
            'student_id' => Yii::t('art/guide', 'Student ID'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'programm_id' => Yii::t('art/guide', 'Programm ID'),
            'speciality_id' => Yii::t('art/guide', 'Speciality ID'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan Subject List'),
            'course' => Yii::t('art/guide', 'Course'),
            'status' => Yii::t('art/guide', 'Status'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Studyplan Subject ID'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Cat ID'),
            'subject_id' => Yii::t('art/guide', 'Subject ID'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type ID'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid ID'),
            'year_time_consult' => Yii::t('art/guide', 'Year Time Consult'),
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Subject Sect Studyplan ID'),
            'direction_id' => Yii::t('art/guide', 'Direction ID'),
            'teachers_id' => Yii::t('art/guide', 'Teachers ID'),
            'consult_schedule_id' => Yii::t('art/guide', 'Consult Schedule ID'),
            'datetime_in' => Yii::t('art/guide', 'Datetime In'),
            'datetime_out' => Yii::t('art/guide', 'Datetime Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory ID'),
            'description' => Yii::t('art/guide', 'Description'),
        ];
    }
}
