<?php

namespace common\models\routine;

use artsoft\behaviors\DateToTimeBehavior;
use Yii;
use common\widgets\yearcalendar\data\DataItem;
use common\widgets\yearcalendar\data\JsExpressionHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%routine}}".
 *
 * @property int $id
 * @property string $description
 * @property int $cat_id
 * @property int $start_timestamp
 * @property int $end_timestamp
 *
 * @property RoutineCat $cat
 */
class Routine extends ActiveRecord implements DataItem
{
    public $start_date;
    public $end_date;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%routine}}';
    }
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => DateToTimeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'start_date',
                    ActiveRecord::EVENT_AFTER_FIND => 'start_date',
                ],
                'timeAttribute' => 'start_timestamp',
                'timeFormat' => 'd.m.Y',
            ],
            [
                'class' => DateToTimeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'end_date',
                    ActiveRecord::EVENT_AFTER_FIND => 'end_date',
                ],
                'timeAttribute' => 'end_timestamp',
                'timeFormat' => 'd.m.Y',
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'cat_id', 'start_date', 'end_date'], 'required'],
            [['start_timestamp', 'end_timestamp'], 'safe'],
            ['start_timestamp', 'compareTimestamp'],
            [['cat_id'], 'integer'],
            [['description'], 'string', 'max' => 1024],
            [['color'], 'string', 'max' => 127],
            ['start_date', 'date', 'format' => 'php:d.m.Y'],
            ['end_date', 'date', 'format' => 'php:d.m.Y'],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => RoutineCat::className(), 'targetAttribute' => ['cat_id' => 'id']],
        ];
    }
    /**
     * сравнение даты начала и окончания/ дата окончания должна быть меньше даты начала
     */
    public function compareTimestamp()
    {
        if (!$this->hasErrors()) {

            if ($this->end_timestamp < $this->start_timestamp) {
                $this->addError('start_date', Yii::t('art/routine', 'The event start date must be less than the end date.'));
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'description' => Yii::t('art', 'Description'),
            'cat_id' => Yii::t('art/routine', 'Catеgory'),
            'start_date' => Yii::t('art/routine', 'Start Date'),
            'start_timestamp' => Yii::t('art/routine', 'Start Date'),
            'end_date' => Yii::t('art/routine', 'End Date'),
            'end_timestamp' => Yii::t('art/routine', 'End Date'),
        ];
    }

    /**
     * Gets query for [[Cat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(RoutineCat::className(), ['id' => 'cat_id']);
    }

    public function getName()
    {
      return  $this->cat->name;
    }
    public function getColor()
    {
      return  $this->cat->color;
    }

    public function getStartDate()
    {
        return JsExpressionHelper::parse($this->start_timestamp);
    }

    public function getEndDate()
    {
        return JsExpressionHelper::parse($this->end_timestamp);
    }
}
