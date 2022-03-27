<?php

namespace common\models\teachers;

use common\models\guidejob\Direction;
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
 * @property int $teachers_id
 * @property float|null $load_time
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
            [['subject_sect_studyplan_id', 'studyplan_subject_id'], 'default', 'value' => 0],
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'version'], 'integer'],
            [['direction_id', 'teachers_id', 'load_time'], 'required'],
            [['load_time'], 'number'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
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
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Subject_Sect_Studyplan ID'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Subject Name'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'load_time' => Yii::t('art/guide', 'Load Time'),
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

    /**
     * Получение всех преподавателей дисциплины по текущему
     * @param $teachers_id
     * @return array
     */
    public static function getTeachersSubjectAll($teachers_id)
    {
        $query1 = self::find()
            ->select('subject_sect_studyplan_id')
            ->distinct()
            ->where(['studyplan_subject_id' => 0])
            ->andWhere(['teachers_id' => $teachers_id])
            ->column();

        $query2 = self::find()
            ->select('studyplan_subject_id')
            ->distinct()
            ->where(['subject_sect_studyplan_id' => 0])
            ->andWhere(['teachers_id' => $teachers_id])
            ->column();

        return self::find()
            ->select('teachers_id')
            ->distinct()
            ->where(['subject_sect_studyplan_id' => $query1])
            ->orWhere(['studyplan_subject_id' => $query2])
            ->column();

    }

    public function getTeachersFullLoad()
    {
        return self::find()
            ->select(new \yii\db\Expression('SUM(load_time)'))
            ->where(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
            ->andWhere(['=', 'studyplan_subject_id', $this->studyplan_subject_id])
            ->andWhere(['=', 'direction_id', $this->direction_id])
            ->scalar();
    }
}
