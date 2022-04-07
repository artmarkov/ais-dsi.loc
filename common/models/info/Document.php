<?php

namespace common\models\info;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

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
 */
class Document extends \artsoft\db\ActiveRecord
{
    public $countFiles;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document';
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
                'attributes' => ['doc_date'],
            ],
            [
                'class' => \artsoft\fileinput\behaviors\FileManagerBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_common_id', 'title', 'doc_date'], 'required'],
            [['user_common_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['doc_date', 'countFiles'], 'safe'],
            [['title'], 'string', 'max' => 127],
            [['description'], 'string', 'max' => 1024],
            [['user_common_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['user_common_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'user_common_id' => Yii::t('art', 'User'),
            'title' => Yii::t('art', 'Title'),
            'description' => Yii::t('art', 'Description'),
            'doc_date' => Yii::t('art/guide', 'Doc Date'),
            'fullName' => Yii::t('art', 'Full Name'),
            'countFiles' => Yii::t('art/guide', 'Count Files'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
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

}
