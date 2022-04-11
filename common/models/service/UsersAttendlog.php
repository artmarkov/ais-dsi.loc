<?php

namespace common\models\service;

use artsoft\behaviors\DateFieldBehavior;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\auditory\Auditory;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "users_attendlog".
 *
 * @property int $id
 * @property int $user_common_id
 * @property int $auditory_id
 * @property int $timestamp_received Ключ выдан
 * @property int|null $timestamp_over Ключ сдан
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property Auditory $auditory
 * @property UserCommon $userCommon
 */
class UsersAttendlog extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_attendlog';
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
                'attributes' => ['timestamp_received', 'timestamp_over'],
                'timeFormat' => 'd.m.Y H:i'
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_common_id', 'auditory_id', 'timestamp_received'], 'required'],
            [['timestamp_received', 'timestamp_over'], 'safe'],
            [['user_common_id', 'auditory_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['auditory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditory::class, 'targetAttribute' => ['auditory_id' => 'id']],
            [['user_common_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['user_common_id' => 'id']],
            [['timestamp_over'], 'compare', 'compareAttribute' => 'timestamp_received', 'operator' => '>', 'message' => 'Время сдачи не может быть меньше или равно времени выдачи.'],
            [['auditory_id'], 'checkKeyExist', 'skipOnEmpty' => false],

        ];
    }

    public function checkKeyExist($attribute, $params)
    {
        if($this->isNewRecord) {

            $timestamp = Schedule::getStartEndDay($this->created_at);

            $thereIsKeyExist = self::find()
                ->where(['auditory_id' => $this->auditory_id])
                ->andWhere(['between', 'created_at', $timestamp[0], $timestamp[1]])
                ->andWhere(['is', 'timestamp_over', null]);

            if ($thereIsKeyExist->exists() === true) {
                $message = 'Ключ от аудитории ' . RefBook::find('auditory_memo_1')->getValue($this->auditory_id) . ' не был сдан';
                $this->addError($attribute, $message);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'user_common_id' => Yii::t('art/guide', 'User Common ID'),
            'auditory_id' => Yii::t('art/guide', 'Auditory'),
            'timestamp_received' => Yii::t('art/guide', 'Time Received'),
            'timestamp_over' => Yii::t('art/guide', 'Time Over'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
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

    /**
     * Gets query for [[UserCommon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCommon()
    {
        return $this->hasOne(UserCommon::class, ['id' => 'user_common_id']);
    }


}
