<?php

namespace common\models\schoolplan;

class SchoolplanResume extends Schoolplan
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['fileManager'] = [
            'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class,
            'form_name' => 'SchoolplanResume',
        ];
        return $behaviors;
    }

}
