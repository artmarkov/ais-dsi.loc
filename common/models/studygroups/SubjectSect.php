<?php

namespace common\models\studygroups;

use artsoft\behaviors\ArrayFieldBehavior;
use common\models\activities\SectSchedule;
use \common\models\education\EducationUnion;
use common\models\subject\Subject;
use common\models\subject\SubjectCategory;
use common\models\subject\SubjectType;
use common\models\subject\SubjectVid;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subject_sect".
 *
 * @property int $id
 * @property int|null $plan_year
 * @property int $union_id
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
 * @property EducationUnion $educationUnion
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
     * @return array
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
            [
                'class' => ArrayFieldBehavior::class,
                'attributes' => ['studyplan_list'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plan_year', 'union_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id'], 'default', 'value' => null],
            [['plan_year', 'union_id', 'course', 'subject_cat_id', 'subject_id', 'subject_type_id', 'subject_vid_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['union_id', 'subject_cat_id', 'studyplan_list'], 'required'],
            [['studyplan_list'], 'safe'],
            [['week_time'], 'number'],
            [['sect_name'], 'string', 'max' => 64],
            [['union_id'], 'exist', 'skipOnError' => true, 'targetClass' => EducationUnion::class, 'targetAttribute' => ['union_id' => 'id']],
            [['subject_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::class, 'targetAttribute' => ['subject_cat_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::class, 'targetAttribute' => ['subject_type_id' => 'id']],
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
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'union_id' => Yii::t('art/guide', 'Education Union'),
            'course' => Yii::t('art/guide', 'Course'),
            'subject_cat_id' => Yii::t('art/guide', 'Subject Category'),
            'subject_id' => Yii::t('art/guide', 'Subject Name'),
            'subject_type_id' => Yii::t('art/guide', 'Subject Type'),
            'subject_vid_id' => Yii::t('art/guide', 'Subject Vid'),
            'sect_name' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_list' => Yii::t('art/guide', 'Studyplan List'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'created_at' => Yii::t('art/guide', 'Created'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
            'version' => Yii::t('art/guide', 'Version'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[SectSchedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSectSchedules()
    {
        return $this->hasMany(SectSchedule::class, ['sect_id' => 'id']);
    }

    /**
     * Gets query for [[Programm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnion()
    {
        return $this->hasOne(EducationUnion::class, ['id' => 'union_id']);
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
        return $this->hasOne(GuideSubjectType::class, ['id' => 'subject_type_id']);
    }

    /**
     * Gets query for [[SubjectVid]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectVid()
    {
        return $this->hasOne(SubjectVid::class, ['id' => 'subject_vid_id']);
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
     * Gets query for [[TeachersLoads]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachersLoads()
    {
        return $this->hasMany(TeachersLoad::class, ['sect_id' => 'id']);
    }
}
