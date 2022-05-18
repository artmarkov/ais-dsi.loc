<?php

namespace common\models\question;

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
 * @property string|null $description
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
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'type_id', 'name', 'label', 'required'], 'required'],
            [['question_id', 'type_id', 'name', 'required', 'sort_order'], 'default', 'value' => null],
            [['question_id', 'type_id', 'name', 'required', 'sort_order'], 'integer'],
            [['label', 'hint', 'default_value', 'description'], 'string', 'max' => 255],
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
            'type_id' => 'Тип атрибута формы',
            'name' => 'Название поля формы(en)',
            'label' => 'Название атрибута формы',
            'hint' => 'Подсказка атрибута формы',
            'required' => 'Обязательность атрибута',
            'default_value' => 'Default Value',
            'description' => 'Description',
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
