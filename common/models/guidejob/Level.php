<?php

namespace common\models\guidejob;

use artsoft\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "guide_teachers_level".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property Teachers[] $teachers
 */
class Level extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_teachers_level';
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
        return $this->hasMany(Teachers::className(), ['level_id' => 'id']);
    }
    
     public static function getLevelList()
    {
        return \yii\helpers\ArrayHelper::map(Level::find()->all(), 'id', 'name');

    }
}
