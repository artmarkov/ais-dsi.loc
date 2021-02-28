<?php

namespace common\models\creative;

use common\models\user\User;
use common\models\own\Department;
Use common\models\user\UserCommon;
use common\models\service\ImageManager;
use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%creative_works}}".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $description
 * @property int $status 0-pending,1-published
 * @property int $comment_status 0-closed,1-open
 * @property int $published_at
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property CreativeCategory $category
 * @property User $updatedBy
 * @property User $createdBy
 * @property CreativeWorksAuthor[] $creativeWorksAuthors
 * @property CreativeWorksDepartment[] $creativeWorksDepartments
 * @property CreativeWorksRevision[] $creativeWorksRevisions
 */
class CreativeWorks extends \yii\db\ActiveRecord
{

    public $gridDepartmentSearch;
    public $gridAuthorSearch;
    public $author_id;
  
    const STATUS_PENDING = 0;
    const STATUS_PUBLISHED = 1;
    const COMMENT_STATUS_CLOSED = 0;
    const COMMENT_STATUS_OPEN = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%creative_works}}';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            [
                'class' => \artsoft\behaviors\ManyHasManyBehavior::className(),
                'relations' => [
                   'departmentItem' => 'department_list',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'status', 'comment_status',  'created_by', 'updated_by'], 'integer'],
            ['name', 'required'],
            [['published_at','created_at', 'updated_at'], 'safe'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 512],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CreativeCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
            ['published_at', 'date', 'timestampAttribute' => 'published_at', 'format' => 'dd-MM-yyyy'],
            ['published_at', 'default', 'value' =>  mktime(0,0,0, date("m", time()), date("d", time()), date("Y", time()))],
            [['department_list'], 'safe'],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'category_id' => Yii::t('art', 'Category ID'),
            'name' => Yii::t('art', 'Name'),
            'description' => Yii::t('art', 'Description'),
            'status' => Yii::t('art', 'Status'),
            'comment_status' => Yii::t('art', 'Comment Status'),
            'published_at' => Yii::t('art', 'Published'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'gridDepartmentSearch' => Yii::t('art/guide', 'Department'),
            'gridAuthorSearch' => Yii::t('art', 'Author'),
        ];
    }

    public function getPublishedDate()
    {
        return Yii::$app->formatter->asDate(($this->isNewRecord) ? time() : $this->published_at);
    }

    public function getCreatedDate()
    {
        return Yii::$app->formatter->asDate(($this->isNewRecord) ? time() : $this->created_at);
    }

    public function getUpdatedDate()
    {
        return Yii::$app->formatter->asDate(($this->isNewRecord) ? time() : $this->updated_at);
    }

    public function getPublishedTime()
    {
        return Yii::$app->formatter->asTime(($this->isNewRecord) ? time() : $this->published_at);
    }

    public function getCreatedTime()
    {
        return Yii::$app->formatter->asTime(($this->isNewRecord) ? time() : $this->created_at);
    }

    public function getUpdatedTime()
    {
        return Yii::$app->formatter->asTime(($this->isNewRecord) ? time() : $this->updated_at);
    }

    public function getPublishedDatetime()
    {
        return "{$this->publishedDate} {$this->publishedTime}";
    }

    public function getCreatedDatetime()
    {
        return "{$this->createdDate} {$this->createdTime}";
    }

    public function getUpdatedDatetime()
    {
        return "{$this->updatedDate} {$this->updatedTime}";
    }

    public function getStatusText()
    {
        return $this->getStatusList()[$this->status];
    }

    public function getCommentStatusText()
    {
        return $this->getCommentStatusList()[$this->comment_status];
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CreativeCategory::className(), ['id' => 'category_id']);
    }
    /* Геттер для названия категории */
    public function getCategoryName()
    {
        return $this->category->name;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreativeWorksAuthors()
    {
        return $this->hasMany(CreativeWorksAuthor::className(), ['works_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreativeWorksDepartments()
    {
        return $this->hasMany(CreativeWorksDepartment::className(), ['works_id' => 'id']);
    }
    /**
     * 
     * @return type
     */
    public function getDepartmentItem()
    {
        return $this->hasMany(Department::className(), ['id' => 'department_id'])
            ->viaTable('creative_works_department', ['works_id' => 'id']);
    }/**
     * 
     * @return type
     */
    public function getAuthorItem()
    {
        return $this->hasMany(UserCommon::className(), ['id' => 'author_id'])
            ->viaTable('creative_works_author', ['works_id' => 'id']);
    }
    /**
     * 
     * @return type
     */
     public static function getDepartmentList()
    {
        return ArrayHelper::map(Department::find()
            ->innerJoin('division', 'division.id = department.division_id')
            ->andWhere(['department.status' => Department::STATUS_ACTIVE])
            ->select('department.id as id, department.name as name, division.name as name_category')
            ->orderBy('division.id')
            ->addOrderBy('department.name')
            ->asArray()->all(), 'id', 'name', 'name_category');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreativeWorksRevisions()
    {
        return $this->hasMany(CreativeWorksRevision::className(), ['works_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return CreativeWorksQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CreativeWorksQuery(get_called_class());
    }
    /**
     * getTypeList
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_PENDING => Yii::t('art', 'Pending'),
            self::STATUS_PUBLISHED => Yii::t('art', 'Published'),
        ];
    }

    /**
     * getStatusOptionsList
     * @return array
     */
    public static function getStatusOptionsList()
    {
        return [
            [self::STATUS_PENDING, Yii::t('art', 'Pending'), 'default'],
            [self::STATUS_PUBLISHED, Yii::t('art', 'Published'), 'primary']
        ];
    }

    /**
     * getCommentStatusList
     * @return array
     */
    public static function getCommentStatusList()
    {
        return [
            self::COMMENT_STATUS_OPEN => Yii::t('art', 'Open'),
            self::COMMENT_STATUS_CLOSED => Yii::t('art', 'Closed')
        ];
    }
    
    public function getImages()
    {
        return $this->hasMany(ImageManager::className(), ['item_id' => 'id'])->orderBy('sort');
    }
    public function getImagesLinks()
    {
        return ArrayHelper::getColumn($this->images, 'imageUrl');
    }
    public function getImagesLinksData()
    {
        return ArrayHelper::toArray($this->images,[
                ImageManager::className() => [
                    'type' => 'type',
                    'filetype' => 'filetype',
                    'downloadUrl' => 'url',
                    'caption'=> 'name',
                    'size'=> 'size',
                    'key'=> 'id',
                ]]
        );
    }
     
   
}
