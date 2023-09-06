<?php

namespace common\models\studyplan;

use Yii;

/**
 * This is the model class for table "thematic_view".
 *
 */
class ThematicView extends StudyplanThematicView
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'thematic_view';
    }

    public function attributeLabels()
    {
        $attr = parent::attributeLabels();

        return $attr;
    }
}
