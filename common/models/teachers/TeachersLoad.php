<?php

namespace common\models\teachers;

use common\models\guidejob\Direction;
use common\models\guidejob\DirectionVid;
use common\models\schedule\ConsultSchedule;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\studyplan\StudyplanSubject;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "teachers_load".
 *
 * @property int $id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int $direction_id
 * @property int $direction_vid_id
 * @property int $teachers_id
 * @property float|null $load_time
 * @property float|null $load_time_consult
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Direction $direction
 * @property SubjectSect $sect
 * @property Teachers $teachers
 */
class TeachersLoad extends \artsoft\db\ActiveRecord
{
    use TeachersLoadTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teachers_load';
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
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'load_time_consult'], 'default', 'value' => 0],
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['direction_id', 'teachers_id', 'load_time', 'direction_vid_id'], 'required'],
            [['load_time', 'load_time_consult'], 'number'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['direction_vid_id'], 'exist', 'skipOnError' => true, 'targetClass' => DirectionVid::class, 'targetAttribute' => ['direction_vid_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['teachers_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'direction_vid_id' => Yii::t('art/teachers', 'Name Direction Vid'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'load_time' => Yii::t('art/guide', 'Load Time'),
            'load_time_consult' => Yii::t('art/guide', 'Load Time Consult'),
            'created_at' => Yii::t('art', 'Created'),
            'updated_at' => Yii::t('art', 'Updated'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
            'userStatus' => Yii::t('art', 'Status'),
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirectionVid()
    {
        return $this->hasOne(DirectionVid::class, ['id' => 'direction_vid_id']);
    }

    /**
     * Gets query for [[Sect]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSect()
    {
        return $this->hasOne(SubjectSectStudyplan::class, ['id' => 'subject_sect_studyplan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyplanSubject()
    {
        return $this->hasOne(StudyplanSubject::class, ['id' => 'studyplan_subject_id']);
    }

    /**
     * Gets query for [[Teachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeachers()
    {
        return $this->hasOne(Teachers::class, ['id' => 'teachers_id']);
    }

    public function beforeDelete()
    {
        $consultIds = ConsultSchedule::find(['id'])->where(['=', 'teachers_load_id', $this->id])->column();
        TeachersLoad::deleteAll(['id' => $consultIds]);

        return parent::beforeDelete();
    }
}
