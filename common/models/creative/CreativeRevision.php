<?php

namespace common\models\creative;

use Yii;

/**
 * This is the model class for table "creative_works_revision".
 *
 * @property int $id
 * @property int $works_id
 * @property int $user_id
 * @property int $timestamp
 *
 * @property CreativeWorks $works
 * @property User $user
 */
class CreativeRevision extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'creative_revision';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['works_id', 'user_id'], 'required'],
            [['works_id', 'user_id', 'timestamp'], 'integer'],
            [['works_id'], 'exist', 'skipOnError' => true, 'targetClass' => CreativeWorks::className(), 'targetAttribute' => ['works_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/creative', 'ID'),
            'works_id' => Yii::t('art/creative', 'Works ID'),
            'user_id' => Yii::t('art/creative', 'User ID'),
            'timestamp' => Yii::t('art/creative', 'Timestamp'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorks()
    {
        return $this->hasOne(CreativeWorks::className(), ['id' => 'works_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
