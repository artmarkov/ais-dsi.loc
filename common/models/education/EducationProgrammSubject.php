<?php

namespace common\models\education;

use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\models\User;
use Yii;

/**
 * This is the model class for table "education_programm_subject".
 *
 * @property int $id
 * @property int $programm_id
 * @property int $subject_cat_id
 * @property int $subject_id
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property EducationProgramm $programm
 * @property GuideSubjectCategory $subjectCat
 * @property Subject $subject
 * @property EducationProgrammSubjectTime[] $educationProgrammSubjectTimes
 */
class EducationProgrammSubject extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'education_programm_subject';
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
            [['programm_id', 'subject_cat_id'], 'required'],
            [['programm_id', 'subject_cat_id', 'subject_id'], 'default', 'value' => null],
            [['programm_id', 'subject_cat_id', 'subject_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::class, 'targetAttribute' => ['programm_id' => 'id']],
            [['subject_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::class, 'targetAttribute' => ['subject_cat_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'programm_id' => Yii::t('art/guide', 'Programm Name'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
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
     * Gets query for [[Programm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramm()
    {
        return $this->hasOne(EducationProgramm::class, ['id' => 'programm_id']);
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
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[EducationProgrammSubjectTimes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEducationProgrammSubjectTimes()
    {
        return $this->hasMany(EducationProgrammSubjectTime::class, ['programm_subject_id' => 'id']);
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
}
