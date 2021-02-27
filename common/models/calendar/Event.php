<?php

namespace common\models\calendar;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property int $id
 * @property int $all_day
 * @property string $title
 * @property string $description
 * @property string $start_timestamp
 * @property string $end_timestamp
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%event}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            [['start_timestamp', 'end_timestamp'], 'required'],
            [['description'], 'string'],
            [['start_timestamp', 'end_timestamp','all_day'], 'safe'],
            ['title', 'string', 'max' => 100],
//            [['start_timestamp', 'end_timestamp'], 'default', 'value' =>  mktime(date("H", time()), date("i", time()),0, date("m", time()), date("d", time()), date("Y", time()))],
//            ['start_timestamp', 'compareTimestamp'],
            [['category_id', 'auditory_id'], 'integer'],
            ];
    }
    /**
     * сравнение даты начала и окончания/ дата окончания должна быть меньше даты начала
     */
    public function compareTimestamp()
    {
        if (!$this->hasErrors()) {

            if ($this->end_timestamp < $this->start_timestamp) {
                $this->addError('start_timestamp', Yii::t('art/calendar', 'The event start date must be greater than the end date.'));
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/calendar', 'ID'),
            'title' => Yii::t('art/calendar', 'Title'),
            'description' => Yii::t('art/calendar', 'Description'),
            'start_timestamp' => Yii::t('art/calendar', 'Start Date'),
            'end_timestamp' => Yii::t('art/calendar', 'End Date'),
            'all_day' => Yii::t('art/calendar', 'All Day'),
            'category_id' => Yii::t('art/calendar', 'Category Name'),
            'auditory_id' => Yii::t('art/guide', 'Auditory Name'),
        ];
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(EventCategory::className(), ['id' => 'category_id']);
    }
/**
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(\common\models\auditory\Auditory::className(), ['id' => 'auditory_id']);
    }

    /* Геттер для названия категории */
    public function getCategoryName()
    {
        return $this->category->name;
    }
    /* Геттер для названия цвета */
    public function getCategoryColor()
    {
        return $this->category->color;
    }
    /* Геттер для названия цвета шрифта*/
    public function getCategoryTextColor()
    {
        return $this->category->text_color;
    }
    /* Геттер для названия цвета рамки*/
    public function getCategoryBorderColor()
    {
        return $this->category->border_color;
    }
    /* геттер определения вида представления события категории в виде фона или бара */
     public function getCategoryRendering() 
    {
        return $this->category->rendering;
    }
}
