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
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_id' => Yii::t('art/guide', 'Studyplan'),
            'student_id' => Yii::t('art/student', 'Student'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'status' => Yii::t('art/guide', 'Status'),
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'load_time' => Yii::t('art/guide', 'Load Time'),
        ];
    }

}
