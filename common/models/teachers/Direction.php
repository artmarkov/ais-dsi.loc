<?php

namespace common\models\teachers;

use Yii;

/**
 * This is the model class for table "{{%teachers_direction}}".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property Cost[] $teachersCosts
 */
class Direction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%teachers_direction}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 128],
            [['slug'], 'string', 'max' => 32],
            [['name','slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/teachers', 'ID'),
            'name' => Yii::t('art/teachers', 'Name'),
            'slug' => Yii::t('art/teachers', 'Slug'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCosts()
    {
        return $this->hasMany(Cost::className(), ['direction_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
   
    public static function getDirectionList()
    {
        return \yii\helpers\ArrayHelper::map(Direction::find()->all(), 'id', 'name');

    }
}
