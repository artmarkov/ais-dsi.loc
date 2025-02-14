<?php

namespace common\models\question;

use artsoft\behaviors\DynamicAttributeBehavior;
use himiklab\sortablegrid\SortableGridBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "question_attribute".
 *
 * @property int $id
 * @property int $question_id
 * @property int $type_id Тип атрибута формы (Строка, Текст, Дата, Дата:время, E-mail, Телефон, Радио-лист, Чек-лист, Файл)
 * @property int $name Название поля формы(en)
 * @property string $label Название атрибута формы
 * @property string|null $hint Подсказка атрибута формы
 * @property int $required Обязательность атрибута (Да, Нет)
 * @property int $unique_flag Уникальное значение (Да, Нет)
 * @property string|null $default_value
 * @property string $description Описание поля
 * @property int|null $sort_order
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Question $question
 * @property QuestionOptions[] $questionOptions
 * @property QuestionValue[] $questionValues
 */
class QuestionAttribute extends \artsoft\db\ActiveRecord
{
    const TYPE_STRING = 1;
    const TYPE_TEXT = 2;
    const TYPE_DATE = 3;
    const TYPE_DATETIME = 4;
    const TYPE_EMAIL = 5;
    const TYPE_PHONE = 6;
    const TYPE_RADIOLIST = 7;
    const TYPE_RADIOLIST_UNIQUE = 77;
    const TYPE_CHECKLIST = 8;
    const TYPE_CHECKLIST_UNIQUE = 88;
    const TYPE_FILE = 9;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question_attribute';
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
                'class' => DynamicAttributeBehavior::class,
                'in_attribute' => 'label',
                'out_attribute' => 'name'
            ],
            'grid-sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort_order',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'type_id', 'label'], 'required'],
            [['question_id', 'type_id', 'required', 'unique_flag', 'sort_order'], 'integer'],
            [['hint'], 'string', 'max' => 512],
            [['description'], 'string', 'max' => 1024],
            [['name', 'label', 'default_value'], 'string', 'max' => 127],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::class, 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Question ID',
            'type_id' => 'Тип поля',
            'name' => 'Название поля',
            'label' => 'Название поля',
            'hint' => 'Подсказка поля',
            'required' => 'Обязательно к заполнению',
            'unique_flag' => 'Уникальное значение',
            'default_value' => 'Значение по умолчанию',
            'description' => 'Описание Поля',
            'sort_order' => Yii::t('art/guide', 'Order'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    public static function getTypeList()
    {
        return array(
            self::TYPE_STRING => 'Строка',
            self::TYPE_TEXT => 'Текст',
            self::TYPE_DATE => 'Дата',
            self::TYPE_DATETIME => 'Дата:время',
            self::TYPE_EMAIL => 'E-mail',
            self::TYPE_PHONE => 'Телефон',
            self::TYPE_RADIOLIST => 'Радио-лист (ед.выбор)',
            self::TYPE_CHECKLIST => 'Чек-лист (мн.выбор)',
            self::TYPE_RADIOLIST_UNIQUE => 'Радио-лист уникальный (ед.выбор)',
            self::TYPE_CHECKLIST_UNIQUE => 'Чек-лист уникальный (мн.выбор)',
            self::TYPE_FILE => 'Файл',
        );
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getTypeValue($val)
    {
        $ar = self::getTypeList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * Gets query for [[QuestionOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionOptions()
    {
        return $this->hasMany(QuestionOptions::className(), ['attribute_id' => 'id'])->orderBy('question_options.id');
    }

    /**
     * Gets query for [[QuestionValues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionValues()
    {
        return $this->hasMany(QuestionValue::className(), ['question_attribute_id' => 'id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->sort_order == null) {
            $this->sort_order = $this->id;
        }
        if (in_array($this->type_id, [self::TYPE_RADIOLIST, self::TYPE_RADIOLIST_UNIQUE, self::TYPE_CHECKLIST, self::TYPE_CHECKLIST_UNIQUE])) {
            $this->unique_flag = 0;
        }

        return parent::beforeSave($insert);
    }
}
