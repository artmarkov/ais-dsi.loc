<?php

namespace common\models\education;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use artsoft\models\User;
use Yii;

/**
 * This is the model class for table "education_programm_subject_time".
 *
 * @property int $id
 * @property int $programm_subject_id
 * @property int $cource
 * @property float|null $week_time
 * @property float|null $cost_week_hour
 * @property float|null $year_time
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property EducationProgrammSubject $programmSubject
 */
class EducationProgrammSubjectTime extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'education_programm_subject_time';
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
            [['cource', 'week_time', 'year_time'], 'required'],
            [['programm_subject_id', 'cource', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['week_time', 'cost_week_hour', 'year_time'], 'number'],
            [['programm_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgrammSubject::class, 'targetAttribute' => ['programm_subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'programm_subject_id' => Yii::t('art/guide', 'Programm Subject'),
            'cource' => Yii::t('art/guide', 'Cource'),
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


    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[ProgrammSubject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgrammSubject()
    {
        return $this->hasOne(EducationProgrammSubject::class, ['id' => 'programm_subject_id']);
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
