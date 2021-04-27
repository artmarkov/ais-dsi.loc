<?php

namespace  common\models\creative;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\behaviors\DateFieldBehavior;
use artsoft\traits\DateTimeTrait;
use common\models\efficiency\TeachersEfficiency;
use common\models\teachers\Teachers;
use common\models\user\UserCommon;
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
            [['category_id', 'name', 'department_list', 'teachers_list'], 'required'],
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

    /**
     * @return array
     */
    public function getTeachersList()
    {
        return \yii\helpers\ArrayHelper::map(Teachers::find()->innerJoin('user_common', 'user_common.id = teachers.user_common_id')
            ->andWhere(['in', 'user_common.status', UserCommon::STATUS_ACTIVE])// заблокированных не добавляем в список
            ->andWhere(['in', 'user_common.user_category', UserCommon::USER_CATEGORY_TEACHERS])// только преподаватели
            ->andWhere(['in', 'user_common.id', $this->teachers_list])
            ->select(['teachers.id as id', "CONCAT(user_common.last_name, ' ',user_common.first_name, ' ',user_common.middle_name) AS name"])
            ->orderBy('user_common.last_name')
            ->asArray()->all(), 'id', 'name');
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert) {
        if ($this->isAttributeChanged('teachers_list')) {
            print_r($this->teachers_list);
            print_r($this->getOldAttribute('teachers_list'));
//            print_r();getTeachersEfficiency()
//            foreach (array_diff($this->getOldAttribute('teachers_list'), $this->teachers_list ) as $id){
//                TeachersEfficiency::deleteAll(['AND', 'teachers_id = :teachers_id', ['NOT IN', 'food_id', [1,2]]], [':restaurant_id' => $postData['resto_id']]);
//            }
        }

        return parent::beforeSave($insert);
    }
}
