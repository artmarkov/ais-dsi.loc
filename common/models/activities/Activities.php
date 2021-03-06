<?php

namespace common\models\activities;

use Yii;
use yii\db\ActiveRecord;
use common\models\auditory\Auditory;
use artsoft\behaviors\DateFieldBehavior;

/**
 * This is the model class for table "activities".
 *search
 * @property int $id
 * @property int $all_day
 * @property string $title
 * @property string $description
 * @property string $start_time
 * @property string $end_time
 * @property Auditory $auditory
 * @property string $auditoryName
 * @property ActivitiesCat $cat
 * @property string $catName
 * @property string $rendering
 * @property string $color
 */
class Activities extends ActiveRecord
{

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
                'class' => DateFieldBehavior::class,
                'attributes' => ['start_time', 'end_time'],
                'timeFormat' => 'd.m.Y H:i'
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
            [['start_time', 'end_time', 'all_day'], 'safe'],
            ['start_time', 'compareTime'],
            [['description'], 'string'],
            ['title', 'string', 'max' => 100],
            [['category_id', 'auditory_id', 'all_day'], 'integer'],
        ];
    }

    /**
     * сравнение даты начала и окончания/ дата окончания должна быть меньше даты начала
     */
    public function compareTime()
    {
        if (!$this->hasErrors()) {

            if ($this->end_time < $this->start_time) {
                $this->addError('start_time', Yii::t('art/calendar', 'The event start date must be greater than the end date.'));
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
        return $this->hasOne(ActivitiesCat::class, ['id' => 'category_id']);
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
        if(isset($eventData['resourceId'])) {
            $this->auditory_id = $eventData['resourceId'];
        }
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(Auditory::class, ['id' => 'auditory_id']);
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

    /* геттер определения вида представления события категории в виде фона или бара */
    public function getRendering()
    {
        return $this->cat->rendering;
    }

}
