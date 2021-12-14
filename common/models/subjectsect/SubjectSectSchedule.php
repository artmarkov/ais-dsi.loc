<?php

namespace common\models\subjectsect;

use common\models\guidejob\Direction;
use common\models\studyplan\StudyplanSubject;
use common\models\teachers\Teachers;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subject_sect_schedule".
 *
 * @property int $id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $studyplan_subject_id
 * @property int $direction_id
 * @property int $teachers_id
 * @property int|null $week_num
 * @property int|null $week_day
 * @property int|null $time_in
 * @property int|null $time_out
 * @property int|null $auditory_id
 * @property string $description
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $version
 *
 * @property Direction $direction
 * @property SubjectSectStudyplan $subjectSectStudyplan
 * @property Teachers $teachers
 */
class SubjectSectSchedule extends \artsoft\db\ActiveRecord
{
    public $teachers_load_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_sect_schedule';
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
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'week_num', 'week_day', 'auditory_id'], 'integer'],
            [['direction_id', 'teachers_id'], 'required'],
            [['time_in', 'time_out', 'teachers_load_id'], 'safe'],
            [['description'], 'string', 'max' => 512],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['subject_sect_studyplan_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectSectStudyplan::class, 'targetAttribute' => ['subject_sect_studyplan_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['teachers_id' => 'id']],
            [['time_out'], 'compare', 'compareAttribute' => 'time_in', 'operator' => '>', 'message' => ''],
            [['auditory_id', 'time_begin'], 'unique', 'targetAttribute' => ['auditory_id', 'time_in'], 'message' => 'time and place is busy place select new one.'],
            [['auditory_id'], 'checkDate', 'skipOnEmpty' => false],
        ];
    }

    public function checkDate($attribute, $params)
    {
        $thereIsAnOverlapping = SubjectSectSchedule::find()->where(
            ['AND',
                ['auditory_id' => $this->auditory_id],
                ['<=', 'time_in', $this->time_in],
                ['>=', 'time_out', $this->time_out]
            ])->exists();

        if ($thereIsAnOverlapping) {
            $this->addError($attribute, 'This place and time is busy, selcet new place or change time.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art/guide', 'ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect ID'),
            'direction_id' => Yii::t('art/guide', 'Direction ID'),
            'teachers_id' => Yii::t('art/guide', 'Teachers ID'),
            'week_num' => Yii::t('art/guide', 'Week Num'),
            'week_day' => Yii::t('art/guide', 'Week Day'),
            'time_in' => Yii::t('art/guide', 'Time In'),
            'time_out' => Yii::t('art/guide', 'Time Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory ID'),
            'description' => Yii::t('art', 'Description'),
            'created_at' => Yii::t('art/guide', 'Created At'),
            'created_by' => Yii::t('art/guide', 'Created By'),
            'updated_at' => Yii::t('art/guide', 'Updated At'),
            'updated_by' => Yii::t('art/guide', 'Updated By'),
            'version' => Yii::t('art/guide', 'Version'),
        ];
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

    public function getStudyplanSubject()
    {
        return $this->hasOne(StudyplanSubject::className(), ['id' => 'studyplan_subject_id']);
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
}
