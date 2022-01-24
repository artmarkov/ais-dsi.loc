<?php

namespace common\models\subjectsect;

use artsoft\behaviors\TimeFieldBehavior;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use artsoft\widgets\Notice;
use common\models\auditory\Auditory;
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
 * @property int $teachers_load_id
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
 * @property TeachersKoad $teachersLoad
 */
class SubjectSchedule extends \artsoft\db\ActiveRecord
{

   // public $teachersLoadId;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_schedule';
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
            [['teachers_load_id', 'week_num', 'week_day', 'auditory_id'], 'integer'],
            [['teachers_load_id', 'week_day', 'auditory_id', 'time_in', 'time_out'], 'required'],
            [['time_in', 'time_out', 'teachersLoadId'], 'safe'],
            [['description'], 'string', 'max' => 512],
            [['teachers_load_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeachersLoad::class, 'targetAttribute' => ['teachers_load_id' => 'id']],
            [['time_in', 'time_out'], 'checkFormatTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['time_out'], 'compare', 'compareAttribute' => 'time_in', 'operator' => '>', 'message' => 'Время окончания не может быть меньше или равно времени начала.'],
//            [['auditory_id', 'time_in'], 'unique', 'targetAttribute' => ['auditory_id', 'time_in'], 'message' => 'time and place is busy place select new one.'],
            //  [['auditory_id'], 'checkScheduleOverLapping', 'skipOnEmpty' => false],
            [['auditory_id'], 'checkScheduleAccompLimit', 'skipOnEmpty' => false],
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

    public function checkScheduleOverLapping($attribute, $params)
    {
        if (SubjectSectScheduleView::getScheduleOverLapping($this)->exists() === true) {
            $this->addError($attribute, 'В одной аудитории накладка по времени!');
        }
    }

    public function checkScheduleAccompLimit($attribute, $params)
    {
        if ($this->direction->parent != null) {
            $thereIsAnAccompLimit = self::find()->where(
                ['AND',
                    ['subject_sect_studyplan_id' => $this->subject_sect_studyplan_id],
                    ['direction_id' => $this->direction->parent],
                    ['auditory_id' => $this->auditory_id],
                    ['<=', 'time_in', $this->encodeTime($this->time_in)],
                    ['>=', 'time_out', $this->encodeTime($this->time_out)],
                    ['=', 'week_day', $this->week_day]
                ]);
            if ($this->getAttribute($this->week_num) !== null) {
                $thereIsAnAccompLimit->andWhere(['=', 'week_num', $this->week_num]);
            }
            if ($thereIsAnAccompLimit->exists() === false) {
                $info = [];
                $message = 'Концертмейстер может работать только в рамках расписания преподавателя';
                $teachersSchedule = self::find()->where(
                    ['AND',
                        ['subject_sect_studyplan_id' => $this->subject_sect_studyplan_id],
                        ['direction_id' => $this->direction->parent]
                    ]);
                foreach ($teachersSchedule->all() as $itemModel) {
                    $string = ' ' . ArtHelper::getWeekValue('short', $itemModel->week_num);
                    $string .= ' ' . ArtHelper::getWeekdayValue('short', $itemModel->week_day) . ' ' . $itemModel->time_in . '-' . $itemModel->time_out;
                    $string .= ' ' . RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id);
                    $info[] = $string;
                }
                $this->addError($attribute, $message);
                Notice::registerWarning($message . ': ' . implode(', ', $info));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('art', 'ID'),
            'teachers_load_id' => Yii::t('art/teachers', 'Teachers Load'),
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
    public function getTeachersLoad()
    {
        return $this->hasOne(TeachersLoad::class, ['id' => 'teachers_load_id']);
    }

    public function getDirectionId()
    {
        return $this->teachersLoad->direction_id;
    }

    public function getTeachersId()
    {
        return $this->teachersLoad->teachers_id;
    }

    public function getDirection()
    {
        return Direction::findOne($this->getDirectionId());
    }
    /**
     * Gets query for [[SubjectSectStudyplan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSectStudyplan()
    {
        return $this->teachersLoad->subject_sect_studyplan_id != 0 ? SubjectSectStudyplan::findOne($this->teachersLoad->subject_sect_studyplan_id) : null;
    }

    /**
     * @return \yii\db\ActiveQuery|null
     */
    public function getStudyplanSubject()
    {
        return $this->teachersLoad->studyplan_subject_id != 0 ? StudyplanSubject::findOne($this->teachersLoad->studyplan_subject_id) : null;
    }

    /**
     * @return bool|\yii\db\ActiveQuery
     */
    public function isSubjectMontly()
    {
        if ($this->teachersLoad->subject_sect_studyplan_id != 0) {
            return $this->subjectSectStudyplan->subjectSect->subjectCat->isMonthly();
        } elseif ($this->teachersLoad->studyplan_subject_id != 0) {
            return $this->studyplanSubject->subjectCat->isMonthly();
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(Auditory::class, ['id' => 'teachers_id']);
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

//    /**
//     * @param $postLoad
//     * @param $studyplan_subject_id
//     * @throws \yii\db\Exception
//     */
//    public function setModelAttributes($postLoad, $studyplan_subject_id)
//    {
//        $teachersLoadId = $postLoad['teachersLoadId'];
//        $model_load = TeachersLoad::findOne($teachersLoadId);
//        $this->teachers_id = $model_load->teachers_id;
//        $this->direction_id = $model_load->direction_id;
//        $this->week_num = $postLoad['week_num'];
//        $this->week_day = $postLoad['week_day'];
//        $this->time_in = $postLoad['time_in'];
//        $this->time_out = $postLoad['time_out'];
//        $this->auditory_id = $postLoad['auditory_id'];
//        $this->description = $postLoad['description'];
//        $modelSubject = StudyplanSubject::findOne($studyplan_subject_id);
//        if ($modelSubject->isIndividual()) {
//            $this->studyplan_subject_id = $studyplan_subject_id;
//            $this->subject_sect_studyplan_id = 0;
//        } else {
//            $this->studyplan_subject_id = 0;
//            $this->subject_sect_studyplan_id = $modelSubject->getSubjectSectStudyplan()->id;
//        }
//        return $this;
//    }
}
