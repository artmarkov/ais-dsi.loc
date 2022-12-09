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
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "education_programm".
 *
 * @property int $id
 * @property int $education_cat_id
 * @property string|null $name
 * @property string|null $short_name
 * @property string|null $term_mastering
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name', 'term_mastering', 'education_cat_id', 'status'], 'required'],
            [['education_cat_id', 'status', 'version'], 'integer'],
            [['status'], 'default', 'value' => null],
            [['name', 'short_name', 'term_mastering'], 'string', 'max' => 512],
            [['description'], 'string', 'max' => 1024],
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
            'short_name' => Yii::t('art', 'Short Name'),
            'term_mastering' => Yii::t('art/guide', 'Term Mastering'),
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

//    public function getCatType()
//    {
//        return $this->educationCat->type_id;
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgrammLevel()
    {
        return $this->hasMany(EducationProgrammLevel::class, ['programm_id' => 'id'])->orderBy('course');
    }

    /**
     * @param $category_id
     * @return array|Subject[]|\common\models\subject\SubjectQuery
     */
    public function getSubjectById($category_id)
    {
        $data = [];
        if ($category_id) {
//            $dep_flag = SubjectCategory::find()->select(['dep_flag'])
//                ->andFilterWhere(['=', 'id', $category_id])
//                ->scalar(); // зависимость от выбора отдела
            $data = Subject::find()->select(['id', 'name']);
//            if ($dep_flag) {
//                foreach ($this->getSpecialityDepartments() as $item => $department_id) {
//                    $data->orWhere(['like', 'department_list', $department_id]);
//
//                }
//            }
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
//            $dep_flag = SubjectCategory::find()->select(['dep_flag'])
//                ->andFilterWhere(['=', 'id', $category_id])
//                ->scalar(); // зависимость от выбора отдела
            $data = Subject::find()->select(['name', 'id']);
//            if ($dep_flag) {
//                foreach ($this->getSpecialityDepartments() as $item => $department_id) {
//                    $data->orWhere(['like', 'department_list', $department_id]);
//
//                }
//            }
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
//    public function getSpecialityDepartments()
//    {
//        $data = [];
//        if ($this->speciality_list) {
//            foreach ($this->speciality_list as $item => $speciality_id) {
//                $department_list = EducationSpeciality::find()->select(['department_list'])->where(['=', 'id', $speciality_id])->scalar();
//
//                $data = array_merge($data, explode(',', $department_list));
//            }
//        }
//        sort($data);
//        return array_unique($data);
//    }
//
//    public static function getSpecialityByProgramm($programm_id)
//    {
//        $data = [];
//        if ($programm_id) {
//            $speciality_list = self::find()->select(['speciality_list'])->where(['=', 'id', $programm_id])->scalar();
//            foreach (explode(',', $speciality_list) as $item => $speciality_id) {
//                $data[$speciality_id] = EducationSpeciality::find()->select(['name'])->where(['=', 'id', $speciality_id])->scalar();
//            }
//        }
//        return $data;
//    }
//
//    public static function getSpecialityByProgrammId($programm_id)
//    {
//        $data = [];
//        if ($programm_id) {
//            $speciality_list = self::find()->select(['speciality_list'])->where(['=', 'id', $programm_id])->scalar();
//            foreach (explode(',', $speciality_list) as $item => $speciality_id) {
//                $data[] = [
//                    'id' => $speciality_id,
//                    'name' => EducationSpeciality::find()->select(['name'])->where(['=', 'id', $speciality_id])->scalar(),
//                ];
//            }
//        }
//        return $data;
//    }
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

    public function copy()
    {
        $modelsEducationProgrammLevel = $this->programmLevel;
        $index = count($modelsEducationProgrammLevel);
        if ($index < $this->term_mastering) {
            if (!empty($modelsEducationProgrammLevel[$index - 1])) {
                $modelEducationProgrammLevel = $modelsEducationProgrammLevel[$index - 1];
                $m = new EducationProgrammLevel();
                $modelEducationProgrammLevel->programm_id = $this->id;
                $modelEducationProgrammLevel->course = $index + 1;
                $modelEducationProgrammLevel->level_id = null;
                $m->setAttributes($modelEducationProgrammLevel->getAttributes());
                $m->save(false);
                $modelsEducationProgrammLevelSubject = $modelEducationProgrammLevel->educationProgrammLevelSubject;
                foreach ($modelsEducationProgrammLevelSubject as $index2 => $modelEducationProgrammLevelSubject) {
                    $mm = new EducationProgrammLevelSubject();
                    $modelEducationProgrammLevelSubject->programm_level_id = $m->id;
                    $mm->setAttributes($modelEducationProgrammLevelSubject->getAttributes());
                    $mm->save(false);
                }
            }
        }
    }
}
