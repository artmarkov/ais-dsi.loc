<?php

namespace common\models\education;

use Yii;

/**
 * This is the model class for table "lesson_progress_view".
 *
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property int|null $plan_year
 * @property int|null $programm_id
 * @property int|null $speciality_id
 * @property int|null $course
 * @property int|null $status
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property int|null $subject_vid_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $lesson_qty
 * @property int|null $current_qty
 * @property int|null $absence_qty
 * @property float|null $current_avg_mark
 * @property float|null $middle_avg_mark
 * @property float|null $finish_avg_mark
 */
class LessonProgressView extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_progress_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/student', 'Student'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'speciality_id' => Yii::t('art/studyplan', 'Speciality Name'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'status' => Yii::t('art/guide', 'Status'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'lesson_qty' => Yii::t('art/studyplan', 'Lesson Qty'),
            'current_qty' => Yii::t('art/studyplan', 'Current Qty'),
            'absence_qty' => Yii::t('art/studyplan', 'Absence Qty'),
            'current_avg_mark' => Yii::t('art/studyplan', 'Current Avg Mark'),
            'middle_avg_mark' => Yii::t('art/studyplan', 'Middle Avg Mark'),
            'finish_avg_mark' => Yii::t('art/studyplan', 'Finish Avg Mark'),
        ];
    }
}
