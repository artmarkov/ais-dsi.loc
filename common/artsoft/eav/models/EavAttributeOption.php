<?php

namespace artsoft\eav\models;

use Yii;

/**
 * This is the model class for table "{{%eav_attribute_option}}".
 *
 * @property integer $id
 * @property integer $attribute_id
 * @property string $value
 *
 * @property EavAttribute[] $eavAttributes
 * @property EavAttribute $attribute
 * @property EavValue[] $eavValues
 */
class EavAttributeOption extends \artsoft\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%eav_attribute_option}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attribute_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'attribute_id' => Yii::t('art/eav', 'Attribute'),
            'value' => Yii::t('art', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getEavAttributes()
//    {
//        return $this->hasMany(EavAttribute::className(), ['default_option_id' => 'id'])
//          ->orderBy(['order' => SORT_DESC]);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttribute($name = '')
    {
        return $this->hasOne(EavAttribute::className(), ['id' => 'attribute_id']);
    }

}