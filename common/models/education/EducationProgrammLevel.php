<?php

namespace common\models\education;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "education_programm_level".
 *
 * @property int $id
 * @property int $programm_id
 * @property int $course
 * @property int $level_id
 * @property float|null $year_time_total
 * @property float|null $cost_month_total
 * @property float|null $cost_year_total
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property EducationProgramm $programm
 * @property EducationProgrammLevelSubject[] $EducationProgrammLevelSubject
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programm_id', 'course'], 'required'],
            [['programm_id'], 'default', 'value' => null],
            [['year_time_total', 'cost_month_total', 'cost_year_total'], 'number'],
            [['year_time_total', 'cost_month_total', 'cost_year_total'], 'default', 'value' => 0],
            [['programm_id', 'level_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::class, 'targetAttribute' => ['programm_id' => 'id']],
            [['level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationLevel::class, 'targetAttribute' => ['level_id' => 'id']],
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
            'course' => Yii::t('art/guide', 'Course'),
            'level_id' => Yii::t('art/guide', 'Education Level'),
            'year_time_total' => Yii::t('art/guide', 'Year Time Total'),
            'cost_month_total' => Yii::t('art/guide', 'Cost Month Total'),
            'cost_year_total' => Yii::t('art/guide', 'Cost Year Total'),
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

    public function getLevel()
    {
        return $this->hasOne(EducationLevel::class, ['id' => 'level_id']);
    }

    /**
     * Gets query for [[EducationProgrammLevelTimes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEducationProgrammLevelSubject()
    {
        return $this->hasMany(EducationProgrammLevelSubject::class, ['programm_level_id' => 'id'])
            ->innerJoin('guide_subject_category', 'guide_subject_category.id = education_programm_level_subject.subject_cat_id')
            ->innerJoin('subject', 'subject.id = education_programm_level_subject.subject_id')
            ->orderBy('guide_subject_category.sort_order ASC, subject.name');
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
//        if ($this->programm->catType == \common\models\education\EducationCat::BASIS_FREE) {
//            $this->level_id = null;
//            $this->cost_month_total = 0;
//        }
        return parent::beforeSave($insert);
    }
}
