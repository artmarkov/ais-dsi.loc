<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\models\User;
use common\models\own\Department;
use common\models\subject\Subject;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\traits\DateTimeTrait;

/**
 * This is the model class for table "education_programm".
 *
 * @property int $id
 * @property int $education_cat_id
 * @property string|null $name
 * @property string|null $speciality_list
 * @property int|null $period_study
 * @property string|null $description
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 * @property EducationCat $educationCat
 */
class EducationProgramm extends \artsoft\db\ActiveRecord
{
    use DateTimeTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'education_programm';
    }
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['speciality_list'],
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'education_cat_id', 'speciality_list', 'period_study', 'status'], 'required'],
            [['education_cat_id', 'period_study', 'status'], 'default', 'value' => null],
            [['education_cat_id', 'period_study', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'version'], 'integer'],
            [['name'], 'string', 'max' => 127],
            [['description'], 'string', 'max' => 1024],
            [['speciality_list'], 'safe'],
            [['education_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationCat::class, 'targetAttribute' => ['education_cat_id' => 'id']],
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
            'id' => Yii::t('art/guide', 'ID'),
            'education_cat_id' => Yii::t('art/guide', 'Education Cat'),
            'name' => Yii::t('art', 'Name'),
            'speciality_list' => Yii::t('art/guide', 'Education Specializations'),
            'period_study' => Yii::t('art/guide', 'Period Study'),
            'description' => Yii::t('art', 'Description'),
            'status' => Yii::t('art', 'Status'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
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
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[EducationCat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEducationCat()
    {
        return $this->hasOne(EducationCat::class, ['id' => 'education_cat_id']);
    }
    /**
     * @return string
     */
    public function getCatName()
    {
        return $this->educationCat->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgrammSubject()
    {
        return $this->hasMany(EducationProgrammSubject::class, ['programm_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * Полный список городов страны по id
     */
    public  function getSubjectById($category_id) {
        $query = [];
        $data = Subject::find()->select(['id','name']);
        $data = $category_id ? $data->where(['like', 'category_list', $category_id]) : $data;
        foreach ($this->getSpecialityDepartments() as $item => $department_id) {
            $query += ['like', 'department_list', $department_id];
        }
        $data = $data->andWhere(['OR', $query]);
        $data = $data->asArray()->all();

        return $data;
    }

    public function getSubjectByCategory($category_id)
    {
        $query = [];
        $data = Subject::find()->select(['name', 'id']);
        $data = $category_id ? $data->where(['like', 'category_list', $category_id]) : $data;
        foreach ($this->getSpecialityDepartments() as $item => $department_id) {
          //  $query += ['like', 'department_list', $department_id];
            array_push($query, ['like', 'department_list', $department_id]);
        }
        print_r(array_values($query));
           // $data = $data->andWhere(['OR', array_keys($query)]);
        $data = $data->indexBy('id')->column();

        return $data;
    }

    /**
     * @return array
     */
    public function getSpecialityDepartments()
    {
        $data = [];
        if($this->speciality_list) {
            foreach ($this->speciality_list as $item => $speciality_id) {
                $department_list = EducationSpeciality::find()->select(['department_list'])->where(['=', 'id', $speciality_id])->scalar();

                $data = array_merge($data, explode(',', $department_list));
            }
        }
        sort($data);
        return array_unique($data);
    }
}
