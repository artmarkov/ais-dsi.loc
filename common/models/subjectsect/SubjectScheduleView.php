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
 * @property int|null $subject_schedule_id
 * @property int|null $week_num
 * @property int|null $week_day
 * @property int|null $time_in
 * @property int|null $time_out
 * @property int|null $auditory_id
 * @property string|null $description
 * @property int|null $status
 */
class SubjectScheduleView extends SubjectSectScheduleView
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
    public function attributeLabels()
    {
        return [
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'teachers_load_week_time' => Yii::t('art/guide', 'Week Time'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect ID'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'subject_schedule_id' => Yii::t('art/guide', 'Subject Schedule'),
            'scheduleDisplay' => Yii::t('art/guide', 'Subject Schedule'),
            'week_num' => Yii::t('art/guide', 'Week Num'),
            'week_day' => Yii::t('art/guide', 'Week Day'),
            'time_in' => Yii::t('art/guide', 'Time In'),
            'time_out' => Yii::t('art/guide', 'Time Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory'),
            'description' => Yii::t('art/guide', 'Description'),

            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/student', 'Student'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'year_time' => Yii::t('art/guide', 'Year Time'),
            'status' => Yii::t('art/guide', 'Status'),
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'speciality_id' => Yii::t('art/studyplan', 'Speciality Name'),
            'course' => Yii::t('art/studyplan', 'Course'),
        ];
    }
}
