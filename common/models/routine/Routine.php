<?php

namespace common\models\routine;

use artsoft\Art;
use artsoft\behaviors\DateFieldBehavior;
use common\models\user\UsersView;
use Yii;
use common\widgets\yearcalendar\data\DataItem;
use common\widgets\yearcalendar\data\JsExpressionHelper;
use artsoft\db\ActiveRecord;

/**
 * This is the model class for table "routine".
 *
 * @property int $id
 * @property string $description
 * @property int $users_id
 * @property int $cat_id
 * @property int $start_date
 * @property int $end_date
 * @property string $name
 * @property string startDate
 * @property string endDate
 *
 * @property RoutineCat $cat
 */
class Routine extends ActiveRecord implements DataItem
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'routine';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['start_date', 'end_date']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[/*'description',*/ 'cat_id', 'start_date', 'end_date'], 'required'],
            [['start_date', 'end_date'], 'safe'],
            ['start_date', 'compareDate'],
            [['cat_id', 'users_id'], 'integer'],
            [['description'], 'string', 'max' => 1024],
            [['color'], 'string', 'max' => 127],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => RoutineCat::class, 'targetAttribute' => ['cat_id' => 'id']],
            [['users_id'], 'required', 'when' => function ($model) {
                return $model->cat_id == '1005';
            }, 'whenClient' => "function (attribute, value) {
                        return $('#routine-cat_id').val() == '1005';
                    }"],
        ];
    }

    /**
     * сравнение даты начала и окончания/ дата окончания должна быть меньше даты начала
     */
    public function compareDate()
    {
        if (!$this->hasErrors()) {

            if (Yii::$app->formatter->asTimestamp($this->end_date) < Yii::$app->formatter->asTimestamp($this->start_date)) {
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
            'end_date' => Yii::t('art/routine', 'End Date'),
            'users_id' => Yii::t('art', 'User'),
        ];
    }

    /**
     * Gets query for [[Cat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(RoutineCat::class, ['id' => 'cat_id']);
    }

    public function getName()
    {
        $name = $this->cat->name;
        if ($this->users_id) {
            $user = UsersView::find()->where(['id' => $this->users_id])->one();
            if ($user) {
                $name .= ' (' . $user->user_name . ')';
            }
        }
        return $name;
    }

    public function getColor()
    {
        return $this->cat->color;
    }

    public function getStartDate()
    {
        return JsExpressionHelper::parse($this->start_date);
    }

    public function getEndDate()
    {
        return JsExpressionHelper::parse($this->end_date);
    }

    /**
     * заданный выходной или воскресение
     * @param $timestamp
     * @return bool
     */
    public static function isDayOff($timestamp)
    {
        return self::find()->joinWith('cat')
                ->where(['AND',
                    ['<=', 'start_date', $timestamp],
                    ['>=', 'end_date', $timestamp - 86399],
                ])->andWhere(['guide_routine_cat.dayoff_flag' => 1])
                ->exists() || date("w", $timestamp) == 0;
    }

    /**
     * Отпуск преподавателей
     * @param $timestamp
     * @return bool
     */
    public static function isVocation($timestamp)
    {
        return self::find()->joinWith('cat')
            ->where(['AND',
                ['<=', 'start_date', $timestamp],
                ['>=', 'end_date', $timestamp - 86399],
            ])->andWhere(['guide_routine_cat.vacation_flag' => 1])
            ->exists();
    }

    /**
     * Каникулы
     * @param $timestamp
     * @return bool
     */
    public static function isHolidays($timestamp)
    {
        return self::find()
            ->where(['AND',
                ['<=', 'start_date', $timestamp],
                ['>=', 'end_date', $timestamp - 86399],
            ])->andWhere(['cat_id' => 1000])
            ->exists();
    }

    /**
     * Праздники
     * @param $timestamp
     * @return bool
     */
    public static function isCelebrations($timestamp)
    {
        return self::find()
            ->where(['AND',
                ['<=', 'start_date', $timestamp],
                ['>=', 'end_date', $timestamp - 86399],
            ])->andWhere(['cat_id' => 1001])
            ->exists();
    }


    /**
     * Отпуск по болезни преподавателей и сотрудников
     * @param $timestamp
     * @param $users_id
     * @return bool
     */
    public static function isDisorder($timestamp, $users_id)
    {
        return self::find()
            ->where(['AND',
                ['<=', 'start_date', $timestamp],
                ['>=', 'end_date', $timestamp - 86399],
            ])
            ->andWhere(['users_id' => $users_id])
            ->andWhere(['cat_id' => 1005])
            ->exists();
    }

    public static function find()
    {
        $query = parent::find();
        if(Art::isFrontend()) {
            $query = $query->andWhere(['!=', 'cat_id', 1005]);
        }
        return $query;
    }
}
