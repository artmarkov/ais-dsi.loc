<?php

namespace common\models\creative;

use Yii;

/**
 * This is the model class for table "{{%creative_category}}".
 *
 * @property int $id
 * @property string $name
 * @property string $remark
 *
 * @property CreativeWorks[] $creativeWorks
 */
class CreativeCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%creative_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'name' => Yii::t('art', 'Name'),
            'description' => Yii::t('art', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreativeWorks()
    {
        return $this->hasMany(CreativeWorks::className(), ['category_id' => 'id']);
    }

    public static function getCreativeCategoryList()
    {
        return  CreativeCategory::find()->select(['name', 'id'])->indexBy('id')->column();
    }
}
