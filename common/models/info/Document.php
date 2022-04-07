<?php

namespace common\models\info;

use artsoft\models\User;
use common\models\user\UserCommon;
use Yii;

/**
 * This is the model class for table "document".
 *
 * @property int $id
 * @property int $user_common_id
 * @property string $title
 * @property string $description
 * @property int $doc_date
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 *
 * @property UserCommon $userCommon
 * @property Users $createdBy0
 * @property Users $updatedBy0
 */
class Document extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_common_id', 'title', 'description', 'doc_date', 'created_at', 'updated_at'], 'required'],
            [['user_common_id', 'doc_date', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['user_common_id', 'doc_date', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title'], 'string', 'max' => 127],
            [['description'], 'string', 'max' => 1024],
            [['user_common_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::className(), 'targetAttribute' => ['user_common_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'user_common_id' => Yii::t('art/guide', 'User Common ID'),
            'title' => Yii::t('art/guide', 'Title'),
            'description' => Yii::t('art/guide', 'Description'),
            'doc_date' => Yii::t('art/guide', 'Doc Date'),
            'created_at' => Yii::t('art/guide', 'Created At'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated At'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[UserCommon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCommon()
    {
        return $this->hasOne(UserCommon::className(), ['id' => 'user_common_id']);
    }

    /**
     * Gets query for [[CreatedBy0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy0()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy0()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
