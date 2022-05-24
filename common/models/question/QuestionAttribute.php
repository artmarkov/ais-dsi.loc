<?php

namespace common\models\question;

use artsoft\behaviors\SluggableBehavior;
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
 * @property string|null $default_value
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
    const TYPE_CHECKLIST = 8;
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
                'class' => SluggableBehavior::className(),
                'in_attribute' => 'label',
                'out_attribute' => 'name',
                'translit' => true
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'type_id', 'label', 'required'], 'required'],
            [['question_id', 'type_id', 'name', 'required', 'sort_order'], 'integer'],
            [['label', 'hint', 'default_value'], 'string', 'max' => 255],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
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
            'default_value' => 'Значение по умолчанию',
            'sort_order' => 'Sort Order',
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
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
        return $this->hasMany(QuestionOptions::className(), ['attribute_id' => 'id']);
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
}
