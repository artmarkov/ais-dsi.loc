<?php

namespace common\models\efficiency;

use Yii;

/**
 * @property int $content_type
 */
class EfficiencyTree extends \kartik\tree\models\Tree
{
    const BONUS_VID = [
        'full' => [
            1 => 'Процент от оклада - %',
            2 => 'В абсолютном значении - руб',
        ],
        'short' => [
            1 => '%',
            2 => 'руб',
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_efficiency_tree';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] =  ['description', 'string', 'max' => 1024];
        $rules[] =  ['value_default', 'string','max' => 127];
        $rules[] =  ['value_default', 'default', 'value' => 0];
        $rules[] =  ['class', 'string','max' => 127];
        $rules[] =  ['bonus_vid_id', 'integer'];

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['description'] = Yii::t('art', 'Description');
        $attr['value_default'] = Yii::t('art/guide', 'Bonus');
        $attr['class'] = Yii::t('art/guide', 'Class');
        $attr['bonus_vid_id'] = Yii::t('art/guide', 'Bonus Vid');

        return $attr;
    }

    /**
     * @return mixed
     */
    public static function getEfficiencyList()
    {
        return  self::find()->where(['disabled' => false])->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return mixed
     */
    public static function getEfficiencyRoots()
    {
        return  self::find()->roots()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * @return mixed
     */
    public static function getEfficiencyLiaves()
    {
        return  self::find()->leaves()->select(['root', 'id'])->indexBy('id')->column();
    }

    /**
     * getBobusVidList
     * @return array
     */
    public static function getBobusVidList($name = 'full')
    {
        return $name == 'full' ? self::BONUS_VID['full'] : self::BONUS_VID['short'];
    }

    /**
     * getBobusVidValue
     * @param string $val
     * @return string
     */
    public static function getBobusVidValue($name = 'full', $val)
    {
        $ar = self::getBobusVidList($name);
        return isset($ar[$val]) ? $ar[$val] : $val;
    }
}
