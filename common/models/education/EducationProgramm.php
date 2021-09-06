<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
use artsoft\models\User;
use common\models\own\Department;
use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use common\models\subject\SubjectVid;
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
            [['name', 'education_cat_id', 'speciality_list', 'status'], 'required'],
            [['education_cat_id', 'status'], 'default', 'value' => null],
            [['education_cat_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'version'], 'integer'],
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
    public function getProgrammLevel()
    {
        return $this->hasMany(EducationProgrammLevel::class, ['programm_id' => 'id']);
    }

    /**
     * @param $category_id
     * @return array|Subject[]|\common\models\subject\SubjectQuery
     */
    public function getSubjectById($category_id)
    {
        $data = [];
        if ($category_id) {
            $dep_flag = SubjectCategory::find()->select(['dep_flag'])
                ->andFilterWhere(['=', 'id', $category_id])
                ->scalar(); // зависимость от выбора отдела
            $data = Subject::find()->select(['id', 'name']);
            if ($dep_flag) {
                foreach ($this->getSpecialityDepartments() as $item => $department_id) {
                    $data->orWhere(['like', 'department_list', $department_id]);

                }
            }
            $data = $data->andFilterWhere(['like', 'category_list', $category_id]);
            $data = $data->andFilterWhere(['=', 'status', Subject::STATUS_ACTIVE]);
            $data = $data->asArray()->all();
        }
        return $data;
    }

    /**
     * Получаем возможные дисциплины программы выбранной категории
     * @param $category_id
     * @return array|\common\models\subject\SubjectQuery
     */
    public function getSubjectByCategory($category_id)
    {
        $data = [];
        if ($category_id) {
            $dep_flag = SubjectCategory::find()->select(['dep_flag'])
                ->andFilterWhere(['=', 'id', $category_id])
                ->scalar(); // зависимость от выбора отдела
            $data = Subject::find()->select(['name', 'id']);
            if ($dep_flag) {
                foreach ($this->getSpecialityDepartments() as $item => $department_id) {
                    $data->orWhere(['like', 'department_list', $department_id]);

                }
            }
            $data = $data->andFilterWhere(['like', 'category_list', $category_id]);
            $data = $data->andFilterWhere(['=', 'status', Subject::STATUS_ACTIVE]);
            $data = $data->indexBy('id')->column();
        }
        return $data;
    }

    /**
     * Получаем все отделы из всех спецификаций Программы
     * @return array
     */
    public function getSpecialityDepartments()
    {
        $data = [];
        if ($this->speciality_list) {
            foreach ($this->speciality_list as $item => $speciality_id) {
                $department_list = EducationSpeciality::find()->select(['department_list'])->where(['=', 'id', $speciality_id])->scalar();

                $data = array_merge($data, explode(',', $department_list));
            }
        }
        sort($data);
        return array_unique($data);
    }

    public static function getSpecialityByProgramm($programm_id)
    {
        $data = [];
        if ($programm_id) {
            $speciality_list = self::find()->select(['speciality_list'])->where(['=', 'id', $programm_id])->scalar();
            foreach (explode(',', $speciality_list) as $item => $speciality_id) {
                $data[$speciality_id] = EducationSpeciality::find()->select(['name'])->where(['=', 'id', $speciality_id])->scalar();
            }
        }
        return $data;
    }

    public static function getSpecialityByProgrammId($programm_id)
    {
        $data = [];
        if ($programm_id) {
            $speciality_list = self::find()->select(['speciality_list'])->where(['=', 'id', $programm_id])->scalar();
            foreach (explode(',', $speciality_list) as $item => $speciality_id) {
                $data[] = [
                    'id' => $speciality_id,
                    'name' => EducationSpeciality::find()->select(['name'])->where(['=', 'id', $speciality_id])->scalar(),
                ];
            }
        }
        return $data;
    }
//
//    public static function getSubjectVidBySubject($subject_id)
//    {
//        $data = [];
//        if ($subject_id) {
//            $vid_list = Subject::find()->select(['vid_list'])->where(['=', 'id', $subject_id])->scalar();
//            foreach (explode(',', $vid_list) as $item => $vid_id) {
//                $data[$vid_id] = SubjectVid::find()->select(['name'])->where(['=', 'id', $vid_id])->scalar();
//            }
//        }
//        return $data;
//    }
//
//    public static function getSubjectVidBySubjectId($subject_id)
//    {
//        $data = [];
//        if ($subject_id) {
//            $vid_list = Subject::find()->select(['vid_list'])->where(['=', 'id', $subject_id])->scalar();
//            foreach (explode(',', $vid_list) as $item => $vid_id) {
//                $data[] = [
//                    'id' => $vid_id,
//                    'name' => SubjectVid::find()->select(['name'])->where(['=', 'id', $vid_id])->scalar(),
//                ];
//            }
//        }
//        return $data;
//    }
}
