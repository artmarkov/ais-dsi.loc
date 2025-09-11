<?php

namespace  common\models\creative;

use artsoft\Art;
use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use common\models\efficiency\TeachersEfficiency;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\models\User;

/**
 * This is the model class for table "creative_works".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $description
 * @property string|null $department_list
 * @property string|null $teachers_list
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $published_at
 * @property int $status
 * @property int $doc_status
 * @property int $signer_id Подписант
 * @property int $author_id' Автор
 * @property int $version
 *
 * @property CreativeRevision[] $creativeRevisions
 * @property CreativeCategory $category
 * @property User $createdBy
 * @property User $updatedBy
 */
class CreativeWorks extends \artsoft\db\ActiveRecord
{
    public $admin_flag;
    public $admin_message;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'creative_works';
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
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['teachers_list', 'department_list'],
            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['published_at'],
                'timeFormat' => 'd.m.Y H:i'
            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['date'],
                'timeFormat' => 'd.m.Y'
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
            [['category_id', 'name', 'department_list', 'teachers_list', 'signer_id'], 'required'],
            [['category_id', 'created_by', 'updated_by', 'status', 'version', 'doc_status', 'author_id'], 'integer'],
            [['created_at', 'updated_at'], 'integer'],
            [['published_at', 'date'], 'safe'],
            [['doc_status'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 0],
            [['name', 'description'], 'string', 'max' => 1024],
            [['place'], 'string', 'max' => 512],
            [['department_list', 'teachers_list'], 'safe'],
            [['date', 'place'], 'required', 'when' => function ($model) {
                return $model->status == self::STATUS_ACTIVE;
            },
                'whenClient' => "function (attribute, value) {
                                return $('input[name=\"CreativeWorks[status]\"]:checked').val() === '1';
                            }"],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CreativeCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['signer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['signer_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['author_id' => 'id']],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/creative', 'ID'),
            'category_id' => Yii::t('art', 'Category'),
            'name' => Yii::t('art/creative', 'Work topic'),
            'description' => Yii::t('art/creative', 'Description'),
            'department_list' => Yii::t('art/guide', 'Department'),
            'teachers_list' => Yii::t('art/creative', 'Аuthors-performers'),
            'published_at' => Yii::t('art', 'Published At'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
            'version' => Yii::t('art', 'Version'),
            'date' => Yii::t('art/guide', 'Date PPK'),
            'place' => Yii::t('art/guide', 'Place PPK'),
            'doc_status' => Yii::t('art/guide', 'Doc Status'),
            'signer_id' => 'Подписант',
            'admin_message' => Yii::t('art/guide', 'Sign Message'),
            'author_id' => 'Автор записи',
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }
    /**
     * Gets query for [[CreativeRevisions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreativeRevisions()
    {
        return $this->hasMany(CreativeRevision::class, ['works_id' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CreativeCategory::class, ['id' => 'category_id']);
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->category->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersEfficiency()
    {
        return $this->hasMany(TeachersEfficiency::class, ['item_id' => 'id'])->andWhere(['class' => \yii\helpers\StringHelper::basename(get_class($this))]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreativeWorksRevisions()
    {
        return $this->hasMany(CreativeRevision::class, ['works_id' => 'id']);
    }

    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => 'Выполнено',
            self::STATUS_INACTIVE => 'Запланировано',
        );
    }

    /**
     * getStatusOptionsList
     * @return array
     */
    public static function getStatusOptionsList()
    {
        return [
            [self::STATUS_ACTIVE, 'Выполнено', 'success'],
            [self::STATUS_INACTIVE, 'Запланировано', 'danger'],
        ];
    }

    public function getUserSign()
    {
        return $this->hasOne(User::class, ['id' => 'signer_id']);
    }

    public static function getAuthorId()
    {
        $id = \Yii::$app->user->id;
        $user = User::findOne($id);
        return $user->userCommon ? $user->userCommon->id : null;
    }

    /**
     * @return bool|string
     */
    public static function getAuthorEmail()
    {
        $id = \Yii::$app->user->id;
        $user = User::findOne($id);
        return $user->email ?? false;
    }

    /**
     * @return bool
     */
    public function isAuthor()
    {
        return $this->author_id == self::getAuthorId();
    }

    public static function getSignerId()
    {
        $id = \Yii::$app->user->id;
        $user = User::findOne($id);
        return $user ? $user->id : null;
    }

    public function isSigner()
    {
        return $this->signer_id == self::getSignerId();
    }

    public function beforeSave($insert)
    {
        if (!$this->author_id && Art::isFrontend()) {
            $this->author_id = self::getAuthorId();
        }

        return parent::beforeSave($insert);
    }

    public function modifMessage()
    {
        $userCommon = UserCommon::findOne($this->author_id);
        $receiverId = $userCommon->user ? $userCommon->user->id : null;
        Yii::$app->mailbox->send($receiverId, 'modif', $this, $this->admin_message);
    }

    public function approveMessage()
    {
        $userCommon = UserCommon::findOne($this->author_id);
        $receiverId = $userCommon->user ? $userCommon->user->id : null;
        Yii::$app->mailbox->send($receiverId, 'approve', $this, $this->admin_message);
    }

    public function sendApproveMessage()
    {
        $receiverId = $this->signer_id;
        Yii::$app->mailbox->send($receiverId, 'send_approve', $this, $this->admin_message);
    }
}
