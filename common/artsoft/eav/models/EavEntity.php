<?php

namespace artsoft\eav\models;

/**
 * This is the model class for table "{{%eav_entity}}".
 *
 * @property integer $id
 * @property integer $model_id
 * @property integer $category_id
 *
 * @property EavAttribute[] $eavAttributes
 */
class EavEntity extends \artsoft\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%eav_entity}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'model_id' => Yii::t('art/eav', 'Model'),
            'category_id' => Yii::t('art', 'Category'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntityModel()
    {
        return $this->hasOne(EavEntityModel::className(), ['id' => 'model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEavAttributes()
    {

        return $this->hasMany(EavAttribute::className(), ['id' => 'attribute_id'])
            ->viaTable('{{%eav_entity_attribute}}', ['entity_id' => 'id']);
        //->orderBy(['eav_entity_attribute.order' => SORT_DESC]);
    }
}