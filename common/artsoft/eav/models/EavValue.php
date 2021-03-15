<?php

namespace artsoft\eav\models;

use artsoft\eav\models\Eav;
use Yii;

/**
 * This is the model class for table "{{%eav_attribute_value}}".
 *
 * @property integer $id
 * @property integer $entity_id
 * @property integer $attribute_id
 * @property string $value
 *
 * @property EavAttribute $attribute
 * @property Eav $entity
 * @property EavAttributeOption $option
 */
class EavValue extends \artsoft\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%eav_value}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['entity_id', 'attribute_id'], 'required'],
            [['entity_id', 'attribute_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
            //[['attributeId'], 'exist', 'skipOnError' => true, 'targetClass' => EavAttribute::className(), 'targetAttribute' => ['attributeId' => 'id']],
            //[['entityId'], 'exist', 'skipOnError' => true, 'targetClass' => Eav::className(), 'targetAttribute' => ['entityId' => 'id']],
            //[['optionId'], 'exist', 'skipOnError' => true, 'targetClass' => EavAttributeOption::className(), 'targetAttribute' => ['optionId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'entity_id' => Yii::t('art/eav', 'Entity'),
            'attribute_id' => Yii::t('art/eav', 'Attribute'),
            'value' => Yii::t('art', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEavAttribute()
    {
        return $this->hasOne(EavAttribute::className(), ['id' => 'attribute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntity()
    {
        return $this->hasOne(Eav::className(), ['id' => 'entity_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(EavAttributeOption::className(), ['id' => 'value']);
    }

}