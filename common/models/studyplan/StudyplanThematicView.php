<?php

namespace common\models\studyplan;

use Yii;

/**
 * This is the model class for table "studyplan_thematic_view".
 *
 */
class StudyplanThematicView extends StudyplanThematic
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_thematic_view';
    }

    public function attributeLabels()
    {
        $attr = parent::attributeLabels();

        $attr['sect_name'] = Yii::t('art/guide', 'Sect Name');
        $attr['subject'] = Yii::t('art/guide', 'Subject');
        $attr['teachers_id'] = Yii::t('art/teachers', 'Teachers');

        return $attr;
    }
}
