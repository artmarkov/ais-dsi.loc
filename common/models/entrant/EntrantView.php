<?php

namespace common\models\entrant;

use Yii;

/**
 * @property float $mid_mark
 * @property string $group_name
 * @property string $fullname
 * @property string $fio
 * @property int $timestamp_in
 * @property int $birth_date
 * @property int $birth_date_age
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
        $attr['group_name'] = Yii::t('art/guide', 'Group');
        $attr['fullname'] = Yii::t('art/student', 'Student');
        $attr['fio'] = Yii::t('art/student', 'Student');
        $attr['birth_date'] = Yii::t('art', 'Birth Date');
        $attr['birth_date_age'] = Yii::t('art', 'Birth Date Age');

        return $attr;
    }

}
