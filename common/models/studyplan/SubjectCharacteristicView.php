<?php

namespace common\models\studyplan;

use Yii;
/**
 * This is the model class for table "subject_characteristic_view".
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
 * @property int|null $subject_characteristic_id
 * @property string|null $description
 */

class SubjectCharacteristicView extends SubjectCharacteristic
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_characteristic_view';
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
            'speciality_id' => Yii::t('art/studyplan', 'Speciality Name'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'status' => Yii::t('art/guide', 'Status'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'subject_characteristic_id' => Yii::t('art/guide', 'Subject Characteristic'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'description' => Yii::t('art', 'Description'),
        ];
    }
}
