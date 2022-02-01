<?php

namespace common\models\teachers;

use Yii;

/**
 * This is the model class for table "teachers_load_view".
 *
 */
class TeachersLoadView extends TeachersLoad
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_load_view';
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
            'load_time' => Yii::t('art/guide', 'Load Time'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
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
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
        ];
    }

}
