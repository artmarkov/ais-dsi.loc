<?php

namespace common\models\schoolplan;

/**
 * This is the model class for table "schoolplan_view".
 *
 * @property int|null $auditory_places Место проведения
 */
class SchoolplanView extends Schoolplan
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolplan_view';
    }

    public function rules()
    {
        $attr = parent::rules();
        $rules[] = ['auditory_places', 'string', 'max' => 1024];

        return $attr;
    }

    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['auditory_places'] = 'Место проведения';

        return $attr;
    }

}
