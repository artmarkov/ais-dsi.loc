<?php

namespace common\models\education;

use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\models\User;
use Yii;

/**
 * This is the model class for table "education_programm_level_subject".
 *
 * @property int $id
 * @property int $programm_level_id
 * @property int $subject_cat_id
 * @property int $subject_id
 * @property float|null $week_time
 * @property float|null $cost_week_hour
 * @property float|null $year_time
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property EducationProgrammLevel $programmLevel
 */
class EducationProgrammLevelSubject extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'education_programm_level_subject';
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
            [['subject_cat_id', 'subject_id', 'week_time', 'year_time'], 'required'],
            [['programm_subject_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['week_time', 'cost_week_hour', 'year_time'], 'number'],
            [['programm_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgrammLevel::class, 'targetAttribute' => ['programm_subject_id' => 'id']],
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
            'programm_level_id' => Yii::t('art/guide', 'Programm Level'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'cost_week_hour' => Yii::t('art/guide', 'Cost Week Hour'),
            'year_time' => Yii::t('art/guide', 'Year Time'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
        ];
    }

    /**
     * @return string
     */
    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[ProgrammLevel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgrammLevel()
    {
        return $this->hasOne(EducationProgrammLevel::class, ['id' => 'programm_level_id']);
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
