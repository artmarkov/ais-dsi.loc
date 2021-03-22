<?php

namespace common\models\activities;

use Yii;
use artsoft\behaviors\DateToTimeBehavior;
use yii\db\ActiveRecord;
use common\models\auditory\Auditory;

/**
 * This is the model class for table "activities".
 *search
 * @property int $id
 * @property int $all_day
 * @property string $title
 * @property string $description
 * @property string $start_timestamp
 * @property string $end_timestamp
 */
class Activities extends ActiveRecord
{
    public $start_time;
    public $end_time;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activities';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => DateToTimeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'start_time',
                    ActiveRecord::EVENT_AFTER_FIND => 'start_time',
                ],
                'timeAttribute' => 'start_timestamp',
                'timeFormat' => 'd.m.Y H:i',
            ],
            [
                'class' => DateToTimeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'end_time',
                    ActiveRecord::EVENT_AFTER_FIND => 'end_time',
                ],
                'timeAttribute' => 'end_timestamp',
                'timeFormat' => 'd.m.Y H:i',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'start_time', 'end_time'], 'required'],
            [['category_id', 'auditory_id'], 'required'],
            [['start_timestamp', 'end_timestamp', 'all_day'], 'safe'],
            ['start_timestamp', 'compareTimestamp'],
            [['description'], 'string'],
            ['title', 'string', 'max' => 100],
            [['category_id', 'auditory_id', 'all_day'], 'integer'],
            [['start_time', 'end_time'], 'date', 'format' => 'php:d.m.Y H:i'],
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
            'title' => Yii::t('art', 'Title'),
            'description' => Yii::t('art', 'Description'),
            'start_timestamp' => Yii::t('art/calendar', 'Start Date'),
            'end_timestamp' => Yii::t('art/calendar', 'End Date'),
            'start_time' => Yii::t('art/calendar', 'Start Date'),
            'end_time' => Yii::t('art/calendar', 'End Date'),
            'all_day' => Yii::t('art/calendar', 'All Day'),
            'category_id' => Yii::t('art/calendar', 'Category Name'),
            'auditory_id' => Yii::t('art/guide', 'Auditory Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(ActivitiesCat::className(), ['id' => 'category_id']);
    }

    /**
     * @param $eventData
     * @return $this
     * @throws \yii\base\InvalidConfigException
     */
    public function getData($eventData)
    {
        $this->start_time = \Yii::$app->formatter->asDatetime($eventData['start']);
        $this->end_time = \Yii::$app->formatter->asDatetime($eventData['end']);
        $this->all_day = $eventData['allDay'] == 'true' ? 1 : 0;
        if($eventData['resourceId'] != null) {
            $this->auditory_id = $eventData['resourceId'];
        }
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(Auditory::className(), ['id' => 'auditory_id']);
    }

    /* Геттер для названия аудитории */
    public function getAuditoryName()
    {
        return $this->auditory->num . ' - ' . $this->auditory->name;
    }

    /* Геттер для названия категории */
    public function getCatName()
    {
        return $this->cat->name;
    }

    /* Геттер для названия цвета */
    public function getColor()
    {
        return $this->cat->color;
    }

    public function getRendering()
    {
        return $this->cat->rendering;
    }

    /* геттер определения вида представления события категории в виде фона или бара */
    public function getCategoryRendering()
    {
        return $this->cat->rendering;
    }
}
