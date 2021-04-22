<?php

namespace common\models\efficiency;

use Yii;

/**
 * @property int $content_type
 */
class EfficiencyTree extends \kartik\tree\models\Tree
{
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

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['description'] = Yii::t('art', 'Description');
        $attr['value_default'] = Yii::t('art/guide', 'Bonus %');

        return $attr;
    }

    public static function getEfficiencyList()
    {
        return  self::find()->where(['disabled' => false])->select(['name', 'id'])->indexBy('id')->column();
    }
}
