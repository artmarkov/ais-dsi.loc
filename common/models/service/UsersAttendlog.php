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
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_common_id'], 'required'],
            [['user_common_id', 'created_at'], 'unique', 'targetAttribute' => ['user_common_id', 'created_at']],
            [['user_common_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['user_common_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['user_common_id' => 'id']],

        ];
    }

//    public function checkKeyExist($attribute, $params)
//    {
//        if ($this->isNewRecord) {
//
//            $timestamp = Schedule::getStartEndDay($this->created_at);
//
//            $thereIsKeyExist = self::find()
//                ->where(['user_common_id' => $this->user_common_id])
//                ->andWhere(['between', 'created_at', $timestamp[0], $timestamp[1]]);
//
//            if ($thereIsKeyExist->exists() === true) {
//                $message = 'Пользователь уже есть в списке на текущий день';
//                $this->addError($attribute, $message);
//            }
//        }
//    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'user_common_id' => Yii::t('art/guide', 'User Common ID'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
        ];
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

    public function getUserAttendlogKey()
    {
        return $this->hasMany(UsersAttendlogKey::class, ['users_attendlog_id' => 'id']);
    }
}
