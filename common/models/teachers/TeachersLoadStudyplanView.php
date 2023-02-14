<?php

namespace common\models\teachers;

use Yii;

/**
 * This is the model class for table "teachers_load_studyplan_view".
 *
 */
class TeachersLoadStudyplanView extends TeachersLoadView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_load_studyplan_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['student_fio'] = Yii::t('art/student', 'Student');
        $attr['subject'] = Yii::t('art/guide', 'Subject');

        return $attr;
    }
}
