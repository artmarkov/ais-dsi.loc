<?php

namespace common\models\subject;

use Yii;

/**
 * This is the model class for table "subject_sect".
 *
 * @property int $id
 * @property int|null $plan_year
 * @property int $programm_id
 * @property int|null $course
 * @property int $subject_cat_id
 * @property int|null $subject_id
 * @property int|null $subject_type_id
 * @property int|null $subject_vid_id
 * @property string|null $sect_name
 * @property string $studyplan_list
 * @property float|null $week_time
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property SectSchedule[] $sectSchedules
 * @property EducationProgramm $programm
 * @property GuideSubjectCategory $subjectCat
 * @property GuideSubjectType $subjectType
 * @property GuideSubjectVid $subjectVid
 * @property Subject $subject
 * @property TeachersLoad[] $teachersLoads
 */
class SubjectSect extends \artsoft\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_sect';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plan_year', 'programm_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'default', 'value' => null],
            [['plan_year', 'programm_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['programm_id', 'subject_cat_id', 'studyplan_list', 'created_at', 'updated_at'], 'required'],
            [['studyplan_list'], 'string'],
            [['week_time'], 'number'],
            [['sect_name'], 'string', 'max' => 64],
            [['programm_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationProgramm::className(), 'targetAttribute' => ['programm_id' => 'id']],
            [['subject_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuideSubjectCategory::className(), 'targetAttribute' => ['subject_cat_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuideSubjectType::className(), 'targetAttribute' => ['subject_type_id' => 'id']],
            [['subject_vid_id'], 'exist', 'skipOnError' => true, 'targetClass' => GuideSubjectVid::className(), 'targetAttribute' => ['subject_vid_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'programm_id' => Yii::t('art/guide', 'Programm ID'),
            'course' => Yii::t('art/guide', 'Course'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Cat ID'),
            'subject_id' => Yii::t('art/guide', 'Subject ID'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type ID'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid ID'),
            'sect_name' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_list' => Yii::t('art/guide', 'Studyplan List'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'created_at' => Yii::t('art/guide', 'Created At'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated At'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
            'version' => Yii::t('art/guide', 'Version'),
        ];
    }

    /**
     * Gets query for [[SectSchedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSectSchedules()
    {
        return $this->hasMany(SectSchedule::className(), ['sect_id' => 'id']);
    }

    /**
     * Gets query for [[Programm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramm()
    {
        return $this->hasOne(EducationProgramm::className(), ['id' => 'programm_id']);
    }

    /**
     * Gets query for [[SubjectCat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCat()
    {
        return $this->hasOne(GuideSubjectCategory::className(), ['id' => 'subject_cat_id']);
    }

    /**
     * Gets query for [[SubjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectType()
    {
        return $this->hasOne(GuideSubjectType::className(), ['id' => 'subject_type_id']);
    }

    /**
     * Gets query for [[SubjectVid]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectVid()
    {
        return $this->hasOne(GuideSubjectVid::className(), ['id' => 'subject_vid_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[TeachersLoads]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersLoads()
    {
        return $this->hasMany(TeachersLoad::className(), ['sect_id' => 'id']);
    }
}
