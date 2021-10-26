<?php

namespace common\models\activities;

use Yii;

/**
 * This is the model class for table "guide_activities_cat".
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property int $rendering как фон или бар
 * @property string $description
 *
 * @property Event[] $events
 */
class ActivitiesCat extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guide_activities_cat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['rendering'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['color'], 'string', 'max' => 32],
            ['color',  'match', 'pattern' => '/#(([a-fA-F0-9]{3}){1,2}|([a-fA-F0-9]{4}){1,2})\b/'], // только hex
            [['description'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/calendar', 'ID'),
            'name' => Yii::t('art', 'Name'),
            'color' => Yii::t('art/calendar', 'Color'),
            'rendering' => Yii::t('art/calendar', 'Background event'),
            'description' => Yii::t('art', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivities()
    {
        return $this->hasMany(Activities::className(), ['category_id' => 'id']);
    }
    
     public static function getActivitiesCatList()
    {
      return self::find()->select(['id', 'name', 'color'])->asArray()->all();
    }

    public static function getCatList()
    {
        return  self::find()->select(['name', 'id'])->indexBy('id')->column();
    }
}
