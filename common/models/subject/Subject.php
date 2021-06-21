<?php

namespace common\models\subject;

use artsoft\behaviors\ArrayFieldBehavior;
use common\models\own\Department;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\subject\SubjectQuery;

/**
 * This is the model class for table "subject".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $department_list
 * @property string $category_list
 * @property string $vid_list
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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject';
    }

    /**
     * Реализация поведения многое ко многим
     * @return  mixed
     */
    public function behaviors()
    {
        return [
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['department_list', 'category_list', 'vid_list'],
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
            'category_list' => Yii::t('art/guide', 'Subject Category'),
            'vid_list' => Yii::t('art/guide', 'Subject Vid'),
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
        return $this->hasMany(SubjectCategory::class, ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectDepartments()
    {
        return $this->hasMany(SubjectDepartment::class, ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectVids()
    {
        return $this->hasMany(SubjectVid::class, ['subject_id' => 'id']);
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
     * Полный список городов страны по id
     */
    public static function getSubjectById($category_id) {
        $data = self::find()->select(['id','name'])
            ->where(['like', 'category_list', $category_id])
            ->asArray()->all();

        return $data;
    }

    public static function getSubjectByCategory($category_id)
    {
        $data = self::find()->select(['name', 'id']);
        $data = $category_id ? $data->where(['like', 'category_list', $category_id]) : $data;
        $data = $data->indexBy('id')->column();

        return $data;
    }
}
