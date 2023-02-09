<?php

namespace common\models\studyplan;

use Yii;

/**

 * @property string|null $education_programm_name
 * @property string|null $education_programm_short_name
 * @property string|null $education_cat_name
 * @property string|null $education_cat_short_name
 * @property string|null $student_fio
 */
class StudyplanView extends Studyplan
{

    public function attributeLabels()
    {

        $attr = parent::attributeLabels();

        $attr['education_programm_name'] = 'Список программ';
        $attr['education_programm_short_name'] = 'Список программ';
        $attr['education_cat_name'] = 'Категория программы';
        $attr['education_cat_short_name'] = 'Категория программы';
        $attr['student_fio'] = 'Ученик';

        return $attr;
    }
}
