<?php

namespace common\models\studyplan;

use Yii;

/**

 * @property string|null $education_programm_name
 * @property string|null $education_programm_short_name
 * @property string|null $education_cat_name
 * @property string|null $education_cat_short_name
 * @property string|null $student_fio
 * @property string|null $subject_type_name
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

        $attr['education_programm_name'] = 'Список программ';
        $attr['education_programm_short_name'] = 'Список программ';
        $attr['education_cat_name'] = 'Категория программы';
        $attr['education_cat_short_name'] = 'Категория программы';
        $attr['student_fio'] = 'Ученик';
        $attr['subject_type_name'] = 'Тип занятий';

        return $attr;
    }
}
