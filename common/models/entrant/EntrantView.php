<?php

namespace common\models\entrant;

use Yii;

/**
 * @property float $mid_mark
 */
class EntrantView extends Entrant
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrant_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['mid_mark'] = Yii::t('art/studyplan', 'Avg Mark');

        return $attr;
    }

}
