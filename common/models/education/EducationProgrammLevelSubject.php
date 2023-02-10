<?php

namespace common\models\education;

use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use common\models\subject\SubjectVid;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "education_programm_level_subject".
 *
 * @property int $id
 * @property int $programm_level_id
 * @property int $subject_cat_id
 * @property int $subject_vid_id
 * @property int $subject_id
 * @property float|null $week_time
 * @property float|null $year_time
 * @property float|null $cost_hour
 * @property float|null $cost_month_summ
 * @property float|null $cost_year_summ
 * @property float|null $year_time_consult
 * @property bool $med_cert
 * @property bool $fin_cert
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
            [['subject_cat_id', 'subject_vid_id', 'week_time', 'year_time'], 'required'],
            [['programm_level_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['week_time', 'year_time', 'cost_hour', 'cost_month_summ', 'cost_year_summ', 'year_time_consult'], 'number'],
            [['week_time', 'year_time', 'cost_hour', 'cost_month_summ', 'cost_year_summ', 'year_time_consult'], 'default', 'value' => 0],
            [['med_cert', 'fin_cert'], 'boolean'],
            [['programm_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgrammLevel::class, 'targetAttribute' => ['programm_level_id' => 'id']],
            [['subject_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::class, 'targetAttribute' => ['subject_cat_id' => 'id']],
            [['subject_vid_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectVid::class, 'targetAttribute' => ['subject_vid_id' => 'id']],
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
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid Name'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'year_time' => Yii::t('art/guide', 'Year Time'),
            'cost_hour' => Yii::t('art/guide', 'Cost Week Hour'),
            'cost_month_summ' => Yii::t('art/guide', 'Month Summ'),
            'cost_year_summ' => Yii::t('art/guide', 'Year Summ'),
            'year_time_consult' => Yii::t('art/guide', 'Year Time Consult'),
            'med_cert' => Yii::t('art/guide', 'Med Cert'),
            'fin_cert' => Yii::t('art/guide', 'Fin Cert'),
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

    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }

    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::class, ['id' => 'subject_cat_id']);
    }

    public function getSubjectVid()
    {
        return $this->hasOne(SubjectVid::class, ['id' => 'subject_vid_id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
//    public function beforeSave($insert)
//    {
//        if ($this->programmLevel->programm->catType != \common\models\education\EducationCat::BASIS_FREE) {
//            $this->year_time_consult = 0;
//        } else {
//            $this->cost_hour = 0;
//            $this->cost_month_summ = 0;
//            $this->cost_year_summ = 0;
//        }
//        return parent::beforeSave($insert);
//    }
}
