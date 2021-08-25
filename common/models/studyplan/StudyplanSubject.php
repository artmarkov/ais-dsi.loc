<?php

namespace common\models\studyplan;

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\education\EducationProgramm;
use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use common\models\subject\SubjectType;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "studyplan_subject".
 *
 * @property int $id
 * @property int $studyplan_id
 * @property int $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property float|null $week_time
 * @property float|null $year_time
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $status
 * @property int $version
 *
 * @property EducationProgramm $studyplan
 * @property SubjectCategory $subjectCat
 * @property SubjectType $subjectType
 * @property Subject $subject
 */
class StudyplanSubject extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studyplan_subject';
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
            [['subject_cat_id', 'subject_id', 'subject_type_id', 'week_time', 'year_time'], 'required'],
            [['studyplan_id', 'subject_cat_id', 'subject_id', 'subject_type_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'version'], 'integer'],
            [['week_time', 'year_time'], 'number'],
            [['studyplan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::class, 'targetAttribute' => ['studyplan_id' => 'id']],
            [['subject_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::class, 'targetAttribute' => ['subject_cat_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::class, 'targetAttribute' => ['subject_type_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/studyplan', 'ID'),
            'studyplan_id' => Yii::t('art/studyplan', 'Studyplan ID'),
            'subject_cat_id' => Yii::t('art/studyplan', 'Subject Cat ID'),
            'subject_id' => Yii::t('art/studyplan', 'Subject ID'),
            'subject_type_id' => Yii::t('art/studyplan', 'Subject Type ID'),
            'week_time' => Yii::t('art/studyplan', 'Week Time'),
            'year_time' => Yii::t('art/studyplan', 'Year Time'),
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
    /**
     * Gets query for [[Studyplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplan()
    {
        return $this->hasOne(EducationProgramm::class, ['id' => 'studyplan_id']);
    }

    /**
     * Gets query for [[SubjectCat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCat()
    {
        return $this->hasOne(SubjectCategory::class, ['id' => 'subject_cat_id']);
    }

    /**
     * Gets query for [[SubjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::class, ['id' => 'subject_type_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }

    public static function getStudyplanSubjectByCategory($category_id, $departments)
    {
        $data = (new \yii\db\Query())
            ->select(['subject_dep_name', 'subject_id'])
            ->from('subject_view')
            ->where(['subject_category_id' => $category_id, 'department_id' => $departments])
            ->indexBy('subject_id')->column();
        return $data;
    }

    public static function getStudyplanSubjectById($category_id) {
        $data = (new \yii\db\Query())
            ->select(['subject_dep_name as name', 'subject_id as id'])
            ->from('subject_view')
            ->where(['subject_category_id' => $category_id, 'department_id' => array_values(RefBook::find('programm_department', 1000)->getList())])
            ->all();

        return $data;
    }
}
