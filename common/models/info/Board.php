<?php

namespace common\models\info;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\models\User;
use artsoft\traits\DateTimeTrait;
use common\models\user\UserCommon;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "board".
 *
 * @property int $id
 * @property int $author_id
 * @property int $category_id
 * @property int $importance_id
 * @property string $title
 * @property string $description
 * @property string|null $recipients_list
 * @property int $board_date
 * @property int $delete_date
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 * @property Users $author
 */
class Board extends \artsoft\db\ActiveRecord
{
    use DateTimeTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const IMPORTANCE_HI = 1;
    const IMPORTANCE_NORM = 0;
    const IMPORTANCE_LOW = -1;

    const CAT_ALL = 0;
    const CAT_EMPLOYEES = 1;
    const CAT_TEACHERS = 2;
    const CAT_STUDENTS = 3;
    const CAT_PARENTS = 4;
    const CAT_SELECT = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'board';
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
                'attributes' => ['recipients_list'],
            ],
            [
                'class' => DateFieldBehavior::class,
                'attributes' => ['board_date', 'delete_date'],
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
            [['author_id', 'category_id', 'title', 'description', 'board_date', 'delete_date'], 'required'],
            [['author_id', 'category_id', 'delete_date'], 'default', 'value' => null],
            [['author_id', 'category_id', 'importance_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'version'], 'integer'],
            [['board_date', 'delete_date', 'recipients_list'], 'safe'],
            ['board_date', 'default', 'value' => date('d.m.Y')],
            ['importance_id', 'default', 'value' => self::IMPORTANCE_NORM],
            [['title'], 'string', 'max' => 127],
            [['description'], 'string', 'max' => 1024],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserCommon::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/info', 'ID'),
            'author_id' => Yii::t('art', 'Author'),
            'category_id' => Yii::t('art', 'Category'),
            'importance_id' => Yii::t('art/info', 'Importance'),
            'title' => Yii::t('art', 'Title'),
            'description' => Yii::t('art', 'Description'),
            'recipients_list' => Yii::t('art/info', 'Recipients'),
            'board_date' => Yii::t('art/info', 'Board Date'),
            'delete_date' => Yii::t('art/info', 'Delete Date'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'status' => Yii::t('art', 'Status'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => Yii::t('art', 'Active'),
            self::STATUS_INACTIVE => Yii::t('art', 'Inactive'),
        );
    }

    public static function getImportanceList()
    {
        return array(
            self::IMPORTANCE_HI => Yii::t('art/info', 'Hi'),
            self::IMPORTANCE_NORM => Yii::t('art/info', 'Normal'),
            self::IMPORTANCE_LOW => Yii::t('art/info', 'Low'),
        );
    }

    public static function getCategoryList()
    {
        return array(
            self::CAT_ALL => Yii::t('art/info', 'Public announcement'),
            self::CAT_STUDENTS => Yii::t('art/info', 'Students personal account'),
            self::CAT_EMPLOYEES => Yii::t('art/info', 'Employers personal account'),
            self::CAT_TEACHERS => Yii::t('art/info', 'Teachers personal account'),
            self::CAT_PARENTS => Yii::t('art/info', 'Parents personal account'),
            self::CAT_SELECT => Yii::t('art/info', 'Selectively'),
        );
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(UserCommon::class, ['id' => 'author_id']);
    }

    public static function getRecipientsList()
    {
        return ArrayHelper::map(UserCommon::find()
            ->andWhere(['status' => UserCommon::STATUS_ACTIVE])
            ->select(['id', 'CONCAT(last_name, \' \',first_name, \' \',middle_name) as fullname', 'user_category as category'])
            ->orderBy('fullname')
            ->asArray()->all(), 'id', 'fullname', 'category');
    }
}