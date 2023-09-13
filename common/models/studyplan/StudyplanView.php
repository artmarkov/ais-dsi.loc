<?php

namespace common\models\studyplan;

use artsoft\helpers\ArtHelper;
use Yii;

/**

 * @property string|null $education_programm_name
 * @property string|null $education_programm_short_name
 * @property string|null $education_cat_name
 * @property string|null $education_cat_short_name
 * @property string|null $student_fio
 * @property string|null $subject_form_name
 */
class StudyplanView extends Studyplan
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_view';
    }

    public function attributeLabels()
    {

        $attr = parent::attributeLabels();

        $attr['education_programm_name'] = 'Программа';
        $attr['education_programm_short_name'] = 'Программа';
        $attr['education_cat_name'] = 'Категория программы';
        $attr['education_cat_short_name'] = 'Категория программы';
        $attr['student_fio'] = 'Ученик';
        $attr['subject_form_name'] = 'Тип занятий';

        return $attr;
    }

    public static function getStudentStudyplanList($id) {
        $model = self::find()->where(['=', 'id', $id])->one();

        if(!$model) return [];

        return \yii\helpers\ArrayHelper::map(self::find()->select('id,education_programm_short_name as name')
            ->where(['=', 'student_id', $model->student_id])
            ->andWhere(['=', 'plan_year', $model->plan_year])
            ->asArray()->all(), 'id', 'name');
    }


}
