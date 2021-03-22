<?php

namespace common\models\user;

use Yii;

/**
 * This is the model class for table "user_family".
 *
 * @property int $id
 * @property int $relation_id
 * @property int $user_main_id
 * @property int $user_slave_id
 *
 * @property User $userMain
 * @property User $userSlave
 * @property UserRelation $relation0
 */
class UserFamily extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_family';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['relation_id', 'user_main_id', 'user_slave_id'], 'integer'],
            ['relation_id', 'required'],
            ['user_main_id', 'unique', 'targetAttribute' => ['user_main_id', 'user_slave_id'],'message' => Yii::t('art/user', 'The relationships of the selected users are already defined.')], // проверка уникальности пары
            ['user_main_id', 'unique', 'targetAttribute' => ['user_main_id', 'relation_id'],'message' => Yii::t('art/user', 'You cannot use the same relationship to different users.')], // проверка уникальности пары
            [['user_main_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_main_id' => 'id']],
            [['user_slave_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_slave_id' => 'id']],
            [['relation_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserRelation::className(), 'targetAttribute' => ['relation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/user', 'ID'),
            'relation_id' => Yii::t('art/user', 'Family Relation'),
            'user_main_id' => Yii::t('art/user', 'User Main ID'),
            'user_slave_id' => Yii::t('art/user', 'User Slave ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMain()
    {
        return $this->hasOne(User::className(), ['id' => 'user_main_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSlave()
    {
        return $this->hasOne(User::className(), ['id' => 'user_slave_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelation0()
    {
        return $this->hasOne(UserRelation::className(), ['id' => 'relation_id']);
    }
}
