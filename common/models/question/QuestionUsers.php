<?php

namespace common\models\question;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "question_users".
 *
 * @property int $id
 * @property int $question_id
 * @property int $users_id
 * @property int|null $read_flag
 * @property int $created_at
 *
 * @property Question $question
 * @property QuestionValue[] $questionValues
 */
class QuestionUsers extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question_users';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id'], 'required'],
            [['question_id', 'users_id', 'read_flag'], 'default', 'value' => null],
            [['question_id', 'users_id', 'read_flag'], 'integer'],
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
            'users_id' => 'Users ID',
            'read_flag' => 'Read Flag',
            'created_at' => Yii::t('art', 'Created'),
        ];
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
     * Gets query for [[QuestionValues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionValues()
    {
        return $this->hasMany(QuestionValue::className(), ['question_users_id' => 'id']);
    }
}
