<?php

namespace common\models\education;

use artsoft\behaviors\ArrayFieldBehavior;
use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\models\User;
use Yii;

/**
 * This is the model class for table "education_programm_level".
 *
 * @property int $id
 * @property int $programm_id
 * @property string $course_list
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property EducationProgramm $programm
 * @property GuideSubjectCategory $subjectCat
 * @property Subject $subject
 * @property EducationProgrammLevelTime[] $EducationProgrammLevelSubject
 */
class EducationProgrammLevel extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'education_programm_level';
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
                'attributes' => ['course_list'],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programm_id', 'subject_cat_id', 'course_list'], 'required'],
            [['programm_id', 'subject_cat_id', 'subject_id'], 'default', 'value' => null],
            [['programm_id', 'subject_cat_id', 'subject_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::class, 'targetAttribute' => ['programm_id' => 'id']],
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
            'course_list' => Yii::t('art/guide', 'Course'),
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
     * Gets query for [[EducationProgrammLevelTimes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEducationProgrammLevelTimes()
    {
        return $this->hasMany(EducationProgrammLevelSubject::class, ['programm_level_id' => 'id']);
    }

    public function getProgrammSubjectTimesForCourse($course)
    {
        return EducationProgrammLevelSubject::find()->where(['programm_level_id' => $this->id])->andWhere(['=', 'cource', $course])->one();
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
