<?php

namespace common\models\question;

use artsoft\widgets\Notice;
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
    const READ_OFF = 0;
    const READ_ON = 1;

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
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => NULL,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id'], 'required'],
            [['users_id'], 'default', 'value' => null],
            [['users_id'], 'checkUsers'],
            [['question_id', 'users_id', 'read_flag'], 'integer'],
            ['read_flag', 'default', 'value' => 0],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::class, 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    public function checkUsers()
    {
        if($this->users_id != null) {
            $check = self::find()->where(['=', 'users_id', $this->users_id]);

            if ($check->exists() === true) {
                $message = 'Пользователь уже заполнил форму.';
                Notice::registerWarning($message);
                $this->addError('users_id', $message);
                return false;
            }
        }
        return true;
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

    /**
     * @return array
     */
    public static function getReadList()
    {
        return array(
            self::READ_OFF => 'В работе',
            self::READ_ON => 'Зарегистрирован',
        );
    }

    /**
     * @param $val
     * @return mixed
     */
    public static function getReadValue($val)
    {
        $ar = self::getReadList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

}
