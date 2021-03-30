<?php

namespace  common\models\creative;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\traits\DateTimeTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
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
 * @property int $view_rights
 * @property int $version
 *
 * @property CreativeRevision[] $creativeRevisions
 * @property CreativeCategory $category
 * @property User $createdBy
 * @property User $updatedBy
 */
class CreativeWorks extends \artsoft\db\ActiveRecord
{
    use DateTimeTrait;

    const VIEW_CLOSE = 0;
    const VIEW_OPEN = 1;

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
            [['category_id', 'name', 'description', 'department_list', 'teachers_list'], 'required'],
            [['category_id', 'created_by', 'updated_by', 'status', 'version'], 'integer'],
            [['created_at', 'updated_at'], 'integer'],
            [['published_at'], 'safe'],
            [['name'], 'string', 'max' => 512],
            [['description'], 'string', 'max' => 1024],
            [['department_list', 'teachers_list'], 'safe'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CreativeCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
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
        ];
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
    public function getCategoryName()
    {
        return $this->category->name;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreativeWorksRevisions()
    {
        return $this->hasMany(CreativeRevision::class, ['works_id' => 'id']);
    }
    
    public static function getStatusList()
    {
        return [
            self::VIEW_CLOSE => Yii::t('art/creative', 'Closed'),
            self::VIEW_OPEN => Yii::t('art/creative', 'Open'),
        ];
    }

    /**
     * getStatusOptionsList
     * @return array
     */
    public static function getStatusOptionsList()
    {
        return [
            [self::VIEW_CLOSE, Yii::t('art/creative', 'Closed'), 'danger'],
            [self::VIEW_OPEN, Yii::t('art/creative', 'Open'), 'success'],
        ];
    }
}
