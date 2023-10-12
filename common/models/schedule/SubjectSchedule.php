<?php

namespace common\models\schedule;

use artsoft\behaviors\TimeFieldBehavior;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use artsoft\models\User;
use artsoft\widgets\Notice;
use common\models\auditory\Auditory;
use common\models\guidejob\Direction;
use common\models\studyplan\StudyplanSubject;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\teachers\TeachersLoad;
use common\models\teachers\TeachersLoadTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject_schedule".
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
 * @property Direction $direction
 */
class SubjectSchedule  extends \artsoft\db\ActiveRecord
{
    use TeachersLoadTrait;

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
            [['week_num'], 'default', 'value' => 0],
            [['description'], 'string', 'max' => 512],
            [['teachers_load_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeachersLoad::class, 'targetAttribute' => ['teachers_load_id' => 'id']],
            [['time_in', 'time_out'], 'checkFormatTime', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['time_out'], 'compare', 'compareAttribute' => 'time_in', 'operator' => '>', 'message' => 'Время окончания не может быть меньше или равно времени начала.'],
            //  [['auditory_id', 'time_in'], 'unique', 'targetAttribute' => ['auditory_id', 'time_in'], 'message' => 'time and place is busy place select new one.'],
            //  [['auditory_id'], 'checkScheduleOverLapping', 'skipOnEmpty' => false],
            //  [['auditory_id'], 'checkScheduleAccompLimit', 'skipOnEmpty' => false],
        ];
    }


    public function checkFormatTime($attribute, $params)
    {
        if (!preg_match('/^([01]?[0-9]|2[0-3])(:|\.)[0-5][0-9]$/', $this->$attribute)) {
            $this->addError($attribute, 'Формат ввода времени не верен.');
        }
    }

    public function checkScheduleOverLapping($attribute, $params)
    {
        if (SubjectScheduleStudyplanView::getScheduleOverLapping($this)->exists() === true) {
            $this->addError($attribute, 'В одной аудитории накладка по времени!');
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
     * Получаем расписание группы или дисциплины ученика
     * @param $subject_sect_studyplan_id
     * @param $studyplan_subject_id
     * @return array
     */
    public static function getSchedule($subject_sect_studyplan_id, $studyplan_subject_id)
    {
        return SubjectSchedule::find()
            ->innerJoin('teachers_load', 'teachers_load.id = subject_schedule.teachers_load_id')
            ->innerJoin('guide_teachers_direction', 'guide_teachers_direction.id = teachers_load.direction_id')
            ->where(
            ['AND',
                ['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id],
                ['=', 'studyplan_subject_id', $studyplan_subject_id],
            ])
            ->andWhere(['is', 'guide_teachers_direction.parent', null])
            ->all();
    }
   /* public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $model = TeachersLoad::find()
            ->where(['=', 'id', $this->teachers_load_id])
            ->andWhere(['=', 'direction_id', 1000])
            ->one();
        if ($model) {
            $modelFind = TeachersLoad::find()
                ->where(['=', 'studyplan_subject_id', $model->studyplan_subject_id])
                ->andWhere(['=', 'load_time', $model->load_time])
                ->andWhere(['=', 'direction_id', 1001])
                ->one();
            if ($modelFind) {
                $m = new SubjectSchedule();
                $m->teachers_load_id = $modelFind->id;
                $m->week_num = $this->week_num;
                $m->week_day = $this->week_day;
                $m->time_in = $this->time_in;
                $m->time_out = $this->time_out;
                $m->auditory_id = $this->auditory_id;
                $m->save(false);
            }
        }
    }*/
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $model = TeachersLoad::find()
                    ->where(['=', 'id', $this->teachers_load_id])
                    ->andWhere(['=', 'direction_id', 1000])
                    ->one();
                if ($model) {
                    $modelFind = TeachersLoad::find()
                        ->where(['=', 'studyplan_subject_id', $model->studyplan_subject_id])
                        ->where(['=', 'subject_sect_studyplan_id', $model->subject_sect_studyplan_id])
                        ->andWhere(['=', 'load_time', $model->load_time])
                        ->andWhere(['=', 'direction_id', 1001])
                        ->one();
                    if ($modelFind) {
                        $m = new SubjectSchedule();
                        $m->teachers_load_id = $modelFind->id;
                        $m->week_num = $this->week_num;
                        $m->week_day = $this->week_day;
                        $m->time_in = Schedule::decodeTime($this->time_in);
                        $m->time_out = Schedule::decodeTime($this->time_out);
                        $m->auditory_id = $this->auditory_id;
                        $m->save(false);
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
