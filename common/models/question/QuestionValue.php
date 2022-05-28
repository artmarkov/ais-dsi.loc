<?php

namespace common\models\question;

use Yii;

/**
 * This is the model class for table "question_value".
 *
 * @property int $id
 * @property int $question_users_id
 * @property int $question_attribute_id
 * @property string|null $question_option_list
 * @property string|null $value_string
 * @property resource|null $value_file
 *
 * @property QuestionAttribute $questionAttribute
 * @property QuestionUsers $questionUsers
 */
class QuestionValue extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_users_id', 'question_attribute_id'], 'required'],
            [['question_users_id', 'question_attribute_id'], 'default', 'value' => null],
            [['question_users_id', 'question_attribute_id'], 'integer'],
            [['value_file'], 'safe'],
            [['value_string', 'question_option_list'], 'string', 'max' => 1024],
            [['question_attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionAttribute::className(), 'targetAttribute' => ['question_attribute_id' => 'id']],
            [['question_users_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionUsers::className(), 'targetAttribute' => ['question_users_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_users_id' => 'Question Users ID',
            'question_attribute_id' => 'Question Attribute ID',
            'question_option_list' => 'Question Option List',
            'value_string' => 'Value String',
            'value_file' => 'Value File',
        ];
    }

    /**
     * Gets query for [[QuestionAttribute]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionAttribute()
    {
        return $this->hasOne(QuestionAttribute::className(), ['id' => 'question_attribute_id']);
    }

    /**
     * Gets query for [[QuestionUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionUsers()
    {
        return $this->hasOne(QuestionUsers::className(), ['id' => 'question_users_id']);
    }
}
