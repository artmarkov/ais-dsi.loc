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
        $attr['department_list'] = Yii::t('art/guide', 'Department');

        return $attr;
    }

    public static function getStudyplanListByTeachers($teachers_id, $plan_year)
    {
        return \yii\helpers\ArrayHelper::map(self::getStudyplanListById($teachers_id, $plan_year), 'id', 'name');
    }

    public static function getStudyplanListById($teachers_id, $plan_year)
    {
        return self::find()
            ->select('studyplan_id as id,  student_fio as name')
            ->distinct('studyplan_id, student_fio')
            ->where(['=', 'teachers_id', $teachers_id])
            ->andWhere(['=', 'plan_year', $plan_year])
            ->asArray()
            ->all();
    }
}
