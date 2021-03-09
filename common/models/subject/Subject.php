<?php

namespace common\models\subject;

use common\models\own\Department;
use common\models\subject\SubjectVidItem;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\subject\SubjectQuery;

/**
 * This is the model class for table "{{%subject}}".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $status
 *
 * @property SubjectCategory[] $subjectCategories
 * @property SubjectDepartment[] $subjectDepartments
 * @property SubjectVid[] $subjectVids
 */
class Subject extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public $gridCategorySearch;
    public $gridVidSearch;
    public $gridDepartmentSearch;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%subject}}';
    }

    /**
     * Реализация поведения многое ко многим
     * @return  mixed
     */
    public function behaviors()
    {
        return [
            [
                'class' => \artsoft\behaviors\ManyHasManyBehavior::className(),
                'relations' => [
                    'subjectCategoryItem' => 'category_list',
                    'departmentItem' => 'department_list',
                    'subjectVidItem' => 'vid_list',
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
            [['status'], 'required'],
            [['name', 'slug', 'department_list', 'category_list', 'vid_list'], 'required'],
            ['status', 'integer'],
            [['name'], 'string', 'max' => 64],
            [['slug'], 'string', 'max' => 32],
            [['department_list', 'category_list', 'vid_list'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'name' => Yii::t('art/guide', 'Name'),
            'slug' => Yii::t('art/guide', 'Slug'),
            'status' => Yii::t('art/guide', 'Status'),
            'department_list' => Yii::t('art/guide', 'Department'),
            'gridDepartmentSearch' => Yii::t('art/guide', 'Department'),
            'category_list' => Yii::t('art/guide', 'Subject Category'),
            'gridCategorySearch' => Yii::t('art/guide', 'Subject Category'),
            'vid_list' => Yii::t('art/guide', 'Subject Vid'),
            'gridVidSearch' => Yii::t('art/guide', 'Subject Vid'),
        ];
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => Yii::t('art', 'Active'),
            self::STATUS_INACTIVE => Yii::t('art', 'Inactive'),
        );
    }

    /**
     * getStatusValue
     *
     * @param string $val
     *
     * @return string
     */
    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCategories()
    {
        return $this->hasMany(SubjectCategory::className(), ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectDepartments()
    {
        return $this->hasMany(SubjectDepartment::className(), ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectVids()
    {
        return $this->hasMany(SubjectVid::className(), ['subject_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return SubjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubjectQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getSubjectCategoryItem()
    {
        return $this->hasMany(SubjectCategoryItem::className(), ['id' => 'category_id'])
            ->viaTable('subject_category', ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getDepartmentItem()
    {
        return $this->hasMany(Department::className(), ['id' => 'department_id'])
            ->viaTable('subject_department', ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getSubjectVidItem()
    {
        return $this->hasMany(SubjectVidItem::className(), ['id' => 'vid_id'])
            ->viaTable('subject_vid', ['subject_id' => 'id']);
    }

    public static function getSubjectCategoryList()
    {
        return ArrayHelper::map(SubjectCategoryItem::find()
            ->select('id, name')
            ->orderBy('sortOrder')
            ->asArray()->all(), 'id', 'name');
    }

    public static function getSubjectVidList()
    {
        return ArrayHelper::map(SubjectVidItem::find()
            ->select('id, name')
            ->asArray()->all(), 'id', 'name');
    }

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
}
