<?php

namespace common\models\subjectsect;

use Yii;

/**
 * This is the model class for table "subject_schedule_view".
 *
 * @property int|null $teachers_load_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property float|null $teachers_load_week_time
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property int|null $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property int|null $subject_vid_id
 * @property float|null $week_time
 * @property float|null $year_time
 * @property int|null $plan_year
 * @property int|null $subject_sect_schedule_id
 * @property int|null $week_num
 * @property int|null $week_day
 * @property int|null $time_in
 * @property int|null $time_out
 * @property int|null $auditory_id
 * @property string|null $description
 * @property int|null $status
 */
class SubjectScheduleView extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_schedule_view';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teachers_load_id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'studyplan_id', 'student_id', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'plan_year', 'subject_sect_schedule_id', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id', 'status'], 'default', 'value' => null],
            [['teachers_load_id', 'subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'studyplan_id', 'student_id', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'plan_year', 'subject_sect_schedule_id', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id', 'status'], 'integer'],
            [['teachers_load_week_time', 'week_time', 'year_time'], 'number'],
            [['description'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'teachers_load_id' => Yii::t('app/guide', 'Teachers Load ID'),
            'subject_sect_studyplan_id' => Yii::t('app/guide', 'Subject Sect Studyplan ID'),
            'studyplan_subject_id' => Yii::t('app/guide', 'Studyplan Subject ID'),
            'direction_id' => Yii::t('app/guide', 'Direction ID'),
            'teachers_id' => Yii::t('app/guide', 'Teachers ID'),
            'teachers_load_week_time' => Yii::t('app/guide', 'Teachers Load Week Time'),
            'studyplan_id' => Yii::t('app/guide', 'Studyplan ID'),
            'student_id' => Yii::t('app/guide', 'Student ID'),
            'subject_cat_id' => Yii::t('app/guide', 'Subject Cat ID'),
            'subject_id' => Yii::t('app/guide', 'Subject ID'),
            'subject_type_id' => Yii::t('app/guide', 'Subject Type ID'),
            'subject_vid_id' => Yii::t('app/guide', 'Subject Vid ID'),
            'week_time' => Yii::t('app/guide', 'Week Time'),
            'year_time' => Yii::t('app/guide', 'Year Time'),
            'plan_year' => Yii::t('app/guide', 'Plan Year'),
            'subject_sect_schedule_id' => Yii::t('app/guide', 'Subject Sect Schedule ID'),
            'week_num' => Yii::t('app/guide', 'Week Num'),
            'week_day' => Yii::t('app/guide', 'Week Day'),
            'time_in' => Yii::t('app/guide', 'Time In'),
            'time_out' => Yii::t('app/guide', 'Time Out'),
            'auditory_id' => Yii::t('app/guide', 'Auditory ID'),
            'description' => Yii::t('app/guide', 'Description'),
            'status' => Yii::t('app/guide', 'Status'),
        ];
    }
}
