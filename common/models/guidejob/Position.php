<?php

namespace common\models\guidejob;

use Yii;

/**
 * This is the model class for table "guide_teachers_position".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property Teachers[] $teachers
 */
class Position extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_teachers_position';
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
    public function getTeachers()
    {
        return $this->hasMany(Teachers::className(), ['position_id' => 'id']);
    }
    
     public static function getPositionList()
    {
        return \yii\helpers\ArrayHelper::map(Position::find()->all(), 'id', 'name');
    }
}
