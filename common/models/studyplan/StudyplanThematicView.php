<?php

namespace common\models\studyplan;

use Yii;

/**
 * This is the model class for table "studyplan_thematic_view".
 *
 * @property int|null $studyplan_id
 * @property int|null $student_id
 * @property int|null $plan_year
 * @property int|null $programm_id
 * @property int|null $course
 * @property int|null $status
 * @property int|null $studyplan_subject_id
 * @property int|null $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property int|null $subject_vid_id
 * @property int|null $studyplan_thematic_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $thematic_category
 * @property int|null $period_in
 * @property int|null $period_out
 */
class StudyplanThematicView extends StudyplanThematic
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_thematic_view';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studyplan_id', 'student_id', 'plan_year', 'programm_id', 'course', 'status', 'studyplan_subject_id', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'studyplan_thematic_id', 'subject_sect_studyplan_id', 'thematic_category', 'period_in', 'period_out'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [

            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/guide', 'Student ID'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'status' => Yii::t('art/guide', 'Status'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'studyplan_thematic_id' => Yii::t('art/guide', 'Studyplan Thematic ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'thematic_category' => Yii::t('art/studyplan', 'Thematic Category'),
            'period_in' => Yii::t('art/studyplan', 'Period In'),
            'period_out' => Yii::t('art/studyplan', 'Period Out'),
        ];
    }
}
