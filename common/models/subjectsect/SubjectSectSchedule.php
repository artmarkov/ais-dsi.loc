<?php

namespace common\models\subjectsect;

use artsoft\behaviors\TimeFieldBehavior;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\models\guidejob\Direction;
use common\models\studyplan\StudyplanSubject;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersLoad;
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

    public $teachersLoadId;

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
            [
                'class' => TimeFieldBehavior::class,
                'attributes' => ['time_in', 'time_out'],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject_sect_studyplan_id', 'studyplan_subject_id', 'direction_id', 'teachers_id', 'week_num', 'week_day', 'auditory_id'], 'integer'],
            [['direction_id', 'teachers_id', 'week_day', 'auditory_id', 'time_in', 'time_out'], 'required'],
            [['subject_sect_studyplan_id', 'studyplan_subject_id'], 'default', 'value' => 0],
            [['time_in', 'time_out', 'teachersLoadId'], 'safe'],
            [['description'], 'string', 'max' => 512],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['teachers_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teachers::class, 'targetAttribute' => ['teachers_id' => 'id']],
            [['time_in', 'time_out'], 'checkFormatTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['time_out'], 'compare', 'compareAttribute' => 'time_in', 'operator' => '>', 'message' => 'Время окончания не может быть меньше или равно времени начала.'],
//            [['auditory_id', 'time_in'], 'unique', 'targetAttribute' => ['auditory_id', 'time_in'], 'message' => 'time and place is busy place select new one.'],
            [['auditory_id'], 'checkDate', 'skipOnEmpty' => false],
        ];
    }

    public function encodeTime($value)
    {
        $t = explode(":", $value);
        return mktime($t[0], $t[1], 0, 1, 1, 70);

    }

    public function checkFormatTime($attribute, $params)
    {
        if (!preg_match('/^([01]?[0-9]|2[0-3])(:|\.)[0-5][0-9]$/', $this->$attribute)) {
            $this->addError($attribute, 'Формат ввода времени не верен.');
        }
    }

    public function checkDate($attribute, $params)
    {
        $thereIsAnOverlapping = SubjectSectSchedule::find()->where(
            ['AND',
                ['!=', 'id', $this->id],
                ['auditory_id' => $this->auditory_id],
                ['OR',
                    ['AND',
                        ['<=', 'time_in', $this->encodeTime($this->time_in)],
                        ['>=', 'time_out', $this->encodeTime($this->time_in)],
                    ],

                    ['AND',
                        ['<=', 'time_in', $this->encodeTime($this->time_out)],
                        ['>=', 'time_out', $this->encodeTime($this->time_out)],
                    ],
                ],
                ['=', 'week_day', $this->week_day]
            ]);
        if ($this->getAttribute($this->week_num) !== null) {
            $thereIsAnOverlapping->andWhere(['=', 'week_num', $this->week_num]);
        }

        if ($thereIsAnOverlapping->exists() === true) {
            $this->addError($attribute, 'Накладка по времени в аудитории.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect'),
            'studyplan_subject_id' => Yii::t('art/guide', 'Studyplan Subject'),
            'direction_id' => Yii::t('art/teachers', 'Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teacher'),
            'week_num' => Yii::t('art/guide', 'Week Num'),
            'week_day' => Yii::t('art/guide', 'Week Day'),
            'time_in' => Yii::t('art/guide', 'Time In'),
            'time_out' => Yii::t('art/guide', 'Time Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory'),
            'description' => Yii::t('art', 'Description'),
            'created_at' => Yii::t('art', 'Created'),
            'created_by' => Yii::t('art', 'Created By'),
            'updated_at' => Yii::t('art', 'Updated'),
            'updated_by' => Yii::t('art', 'Updated By'),
            'version' => Yii::t('art', 'Version'),
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
     * Gets query for [[SubjectSectStudyplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSectStudyplan()
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

    /**
     * Геттер $teachersLoadId
     * @return false|int|string|null
     */
    public function getTeachersLoadId()
    {
        return TeachersLoad::find()
            ->where(['=', 'teachers_id', $this->teachers_id])
            ->andWhere(['=', 'direction_id', $this->direction_id])
            ->andWhere(['=', 'studyplan_subject_id', $this->studyplan_subject_id])
            ->andWhere(['=', 'subject_sect_studyplan_id', $this->subject_sect_studyplan_id])
            ->scalar();
    }

    /**
     * @return string
     */
    public function getTeachersScheduleDisplay()
    {
        $auditory = RefBook::find('auditory_memo_1')->getValue($this->auditory_id);
        $teachers = RefBook::find('teachers_fio')->getValue($this->teachers_id);
        $direction = $this->direction->slug;
        $string = $this->week_num != 0 ? ' ' . ArtHelper::getWeekList('short')[$this->week_num] : null;
        $string .= ' ' . ArtHelper::getWeekdayList('short')[$this->week_day] . ' ' . $this->time_in . '-' . $this->time_out . '->(' . $auditory . ')';
        $string .= '->' . $teachers . '(' . $direction . ')';
        return $string;
    }

    /**
     * @param $postLoad
     * @param $studyplan_subject_id
     * @throws \yii\db\Exception
     */
    public function setModelAttributes($postLoad, $studyplan_subject_id)
    {
        $teachersLoadId = $postLoad['teachersLoadId'];
        $model_load = TeachersLoad::findOne($teachersLoadId);
        $this->teachers_id = $model_load->teachers_id;
        $this->direction_id = $model_load->direction_id;
        $this->week_num = $postLoad['week_num'];
        $this->week_day = $postLoad['week_day'];
        $this->time_in = $postLoad['time_in'];
        $this->time_out = $postLoad['time_out'];
        $this->auditory_id = $postLoad['auditory_id'];
        $this->description = $postLoad['description'];
        $modelSubject = StudyplanSubject::findOne($studyplan_subject_id);
        if ($modelSubject->isIndividual()) {
            $this->studyplan_subject_id = $studyplan_subject_id;
            $this->subject_sect_studyplan_id = 0;
        } else {
            $this->studyplan_subject_id = 0;
            $this->subject_sect_studyplan_id = $modelSubject->getSubjectSectStudyplan()->id;
        }
        return $this;
    }

    public function setTeachersLoadModelCopy()
    {
        $model_load = TeachersLoad::findOne($this->teachersLoadId);
        $this->setAttribute('teachers_id', $model_load->teachers_id);
        $this->setAttribute('direction_id', $model_load->direction_id);
        $this->setAttribute('subject_sect_studyplan_id', $model_load->subject_sect_studyplan_id);
        $this->setAttribute('studyplan_subject_id', $model_load->studyplan_subject_id);

        return $this;
    }
}
