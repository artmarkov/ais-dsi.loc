<?php

namespace common\models\studyplan;

use artsoft\models\User;
use artsoft\traits\DateTimeTrait;
use common\models\education\EducationProgramm;
use common\models\education\EducationSpeciality;
use common\models\students\Student;
use common\models\subject\Subject;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "studyplan".
 *
 * @property int $id
 * @property int $student_id
 * @property int $programm_id
 * @property int speciality_id
 * @property int|null $course
 * @property int|null $plan_year
 * @property string|null $description
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 * @property EducationProgramm $programm
 * @property Student $student
 */
class Studyplan extends \artsoft\db\ActiveRecord
{
    use DateTimeTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'programm_id', 'speciality_id', 'course', 'plan_year'], 'required'],
            [['student_id', 'programm_id', 'speciality_id', 'course', 'plan_year', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'version'], 'integer'],
            [['description'], 'string', 'max' => 1024],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::class, 'targetAttribute' => ['programm_id' => 'id']],
            [['speciality_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationSpeciality::class, 'targetAttribute' => ['speciality_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/studyplan', 'ID'),
            'student_id' => Yii::t('art/student', 'Student'),
            'programm_id' => Yii::t('art/studyplan', 'Education Programm'),
            'speciality_id' => Yii::t('art/studyplan', 'Speciality Name'),
            'course' => Yii::t('art/studyplan', 'Course'),
            'plan_year' => Yii::t('art/studyplan', 'Plan Year'),
            'description' => Yii::t('art', 'Description'),
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

    /**
     * Gets query for [[Programm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramm()
    {
        return $this->hasOne(EducationProgramm::class, ['id' => 'programm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpeciality()
    {
        return $this->hasOne(EducationSpeciality::class, ['id' => 'speciality_id']);
    }

    /**
     * Получаем первую категорию дисциплины из спецификации
     * @return array
     */
    public  function getTypeScalar()
    {
        $subject_type_list = EducationSpeciality::find()
            ->select(['subject_type_list'])
            ->where(['=', 'id', $this->speciality_id])
            ->scalar();
        $subject_type = explode(',', $subject_type_list);
        return $subject_type[0];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplanSubject()
    {
        return $this->hasMany(StudyplanSubject::class, ['studyplan_id' => 'id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    /**
     * @param $category_id
     * @return array|Subject[]|\common\models\subject\SubjectQuery
     */
    public function getSubjectById($category_id)
    {
        $data = [];
        if ($category_id) {
            $data = Subject::find()->select(['id', 'name']);
            foreach ($this->getSpecialityDepartments() as $item => $department_id) {
                $data->orWhere(['like', 'department_list', $department_id]);

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
            $data = Subject::find()->select(['name', 'id']);
            foreach ($this->getSpecialityDepartments() as $item => $department_id) {
                $data->orWhere(['like', 'department_list', $department_id]);

            }
            $data = $data->andFilterWhere(['like', 'category_list', $category_id]);
            $data = $data->andFilterWhere(['=', 'status', Subject::STATUS_ACTIVE]);
            $data = $data->indexBy('id')->column();
        }
        return $data;
    }

    /**
     * Получаем все отделы из спецификации
     * @return array
     */
    public function getSpecialityDepartments()
    {
        $department_list = EducationSpeciality::find()
            ->select(['department_list'])
            ->where(['=', 'id', $this->speciality_id])
            ->scalar();
        $data = explode(',', $department_list);
        sort($data);
        return $data;
    }

}
