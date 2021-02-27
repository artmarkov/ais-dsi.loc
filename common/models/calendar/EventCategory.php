<?php

namespace common\models\calendar;

use Yii;

/**
 * This is the model class for table "{{%event_category}}".
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $text_color
 * @property string $border_color
 * @property int $rendering как фон или бар
 * @property string $description
 *
 * @property Event[] $events
 */
class EventCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%event_category}}';
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
            [['color', 'text_color', 'border_color'], 'string', 'max' => 32],
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
            'name' => Yii::t('art/calendar', 'Name'),
            'color' => Yii::t('art/calendar', 'Color'),
            'text_color' => Yii::t('art/calendar', 'Text Color'),
            'border_color' => Yii::t('art/calendar', 'Border Color'),
            'rendering' => Yii::t('art/calendar', 'Rendering'),
            'description' => Yii::t('art/calendar', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['category_id' => 'id']);
    }
    
     public static function getEventCategoryList()
    {
      return self::find()->select(['id', 'name', 'color', 'text_color', 'border_color'])->asArray()->all();
    }
}
