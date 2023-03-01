<?php

namespace common\models\activities;

use common\models\teachers\Teachers;

/**
 * This is the model class for table "activities_teachers_view".
 * @property integer $teachers_id
 */
class ActivitiesTeachersView extends Activities
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activities_teachers_view';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'teachers_id']);
    }

}
