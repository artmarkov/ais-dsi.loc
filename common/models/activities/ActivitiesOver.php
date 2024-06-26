<?php

namespace common\models\activities;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use common\models\auditory\Auditory;
use common\models\schoolplan\Schoolplan;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "activities_over".
 *
 * @property int $id
 * @property string|null $title Название мероприятия
 * @property int $over_category Категория мероприятия (подготовка, штатно, замена, отмена и пр.)
 * @property int $datetime_in Дата и время начала
 * @property int $datetime_out Дата и время окончания
 * @property int|null $auditory_id Аудитория
 * @property string|null $department_list Отделы
 * @property string|null $executors_list Ответственные
 * @property string|null $executor_name Ответственный, введенный вручную
 * @property string|null $description Описание мероприятия
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Auditory $auditory
 */
class ActivitiesOver extends \artsoft\db\ActiveRecord
{
    public $executorFlag;
    public $cloneFlag;
    public $cloneDatetime;

    const OVER_CATEGORY = [
        1 => 'Внеплановое мероприятие',
        2 => 'Подготовка к мероприятию',
        3 => 'Замена расписания занятий',
        4 => 'Отмена расписания звнятий',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activities_over';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['datetime_in', 'datetime_out'],
                'timeFormat' => 'd.m.Y H:i'
            ],
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['department_list', 'executors_list'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['over_category', 'title', 'datetime_in', 'datetime_out', 'auditory_id'/*, 'department_list'*/], 'required'],
            [['over_category', 'auditory_id'], 'integer'],
            [['over_category'], 'default', 'value' => 0],
            [['datetime_in', 'datetime_out'], 'safe'],
            [['cloneDatetime'], 'date'],
            [['department_list', 'executors_list'], 'safe'],
            [['title'], 'string', 'max' => 512],
            [['executor_name'], 'string', 'max' => 127],
            [['description'], 'string'],
            [['executorFlag', 'cloneFlag'], 'boolean'],
            [['auditory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditory::class, 'targetAttribute' => ['auditory_id' => 'id']],
            [['datetime_in', 'datetime_out'], 'checkFormatDateTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['datetime_out'], 'compareTimestamp', 'skipOnEmpty' => false],
            [['executors_list'], 'required', 'when' => function ($model) {
                return $model->executorFlag == false;
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"activitiesover-executorflag\"]').prop('checked') === false;
                            }"],
            [['executor_name'], 'required', 'when' => function ($model) {
                return $model->executorFlag == true;
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[id=\"activitiesover-executorflag\"]').prop('checked') === true;
                            }"],
        ];
    }

    public function compareTimestamp($attribute, $params, $validator)
    {
        $timestamp_in = Yii::$app->formatter->asTimestamp($this->datetime_in);
        $timestamp_out = Yii::$app->formatter->asTimestamp($this->datetime_out);

        if ($this->datetime_out && $timestamp_in >= $timestamp_out) {
            $message = 'Время окончания мероприятия не может быть меньше или равно времени начала.';
            $this->addError($attribute, $message);
        }
    }

    public function checkFormatDateTime($attribute, $params)

    {
        if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])(-|\.)(0[1-9]|1[0-2])(-|\.)[0-9]{4}(\s)([01]?[0-9]|2[0-3])(:|\.)[0-5][0-9]$/", $this->$attribute)) {
            $this->addError($attribute, 'Формат ввода даты и времени не верен.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название мероприятия',
            'over_category' => 'Категория мероприятия',
            'datetime_in' => 'Дата и время начала',
            'datetime_out' => 'Дата и время окончания',
            'auditory_id' => 'Аудитория',
            'department_list' => 'Отделы',
            'executors_list' => 'Ответственные',
            'executor_name' => 'Ответственный(вручную)',
            'executorFlag' => 'Ввести вручную',
            'description' => 'Описание мероприятия',
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'cloneFlag' => 'Клонировать по дням недели',
            'cloneDatetime' => 'до Даты включительно',
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Auditory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(Auditory::class, ['id' => 'auditory_id']);
    }


    public function getDependence()
    {
        if ($this->over_category == 2) {
            return $this->hasOne(Schoolplan::class, ['activities_over_id' => 'id']);
        }
    }

    /**
     * getOverCategoryList
     * @return array
     */
    public static function getOverCategoryList()
    {
        return self::OVER_CATEGORY;
    }

    /**
     * getOverCategoryValue
     * @param string $val
     * @return string
     */
    public static function getOverCategoryValue($val)
    {
        $ar = self::getOverCategoryList();
        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    public function afterFind()
    {
        $this->executorFlag = $this->executors_list == '' ? true : false;
        parent::afterFind();
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->cloneFlag && $this->isNewRecord) {
            $stopTime = Yii::$app->formatter->asTimestamp($this->cloneDatetime) + 86400;
            $delta = 60 * 60 * 24 * 7;
            $i = 1;
            if ($stopTime - Yii::$app->formatter->asTimestamp($this->datetime_in) >= $delta) {
                do {
                    $model = new self();
                    $model->setAttributes($this->getAttributes());
                    $timestamp_in = Yii::$app->formatter->asTimestamp($this->datetime_in) + $delta * $i;
                    $timestamp_out = Yii::$app->formatter->asTimestamp($this->datetime_out) + $delta * $i;
                    $model->datetime_in = Yii::$app->formatter->asDatetime($timestamp_in);
                    $model->datetime_out = Yii::$app->formatter->asDatetime($timestamp_out);
                    $model->save();
                    // echo '<pre>' . print_r($model, true) . '</pre>';
                    $i++;
                } while ($timestamp_in + $delta < $stopTime);
            }
        }
        return parent::save($runValidation, $attributeNames);
    }



    public function beforeSave($insert)
    {
        if ($this->executorFlag) {
            $this->executors_list = null;
        } else {
            $this->executor_name = null;
        }
        return parent::beforeSave($insert);
    }
}
