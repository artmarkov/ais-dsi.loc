<?php

namespace common\models\subjectsect;

use artsoft\behaviors\TimeFieldBehavior;
use artsoft\helpers\Schedule;
use common\models\auditory\Auditory;
use common\models\guidejob\Direction;
use common\models\studyplan\StudyplanSubject;
use artsoft\helpers\RefBook;
use artsoft\helpers\ArtHelper;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersLoad;
use Yii;
use artsoft\widgets\Notice;
use artsoft\widgets\Tooltip;

/**
 * This is the model class for table "subject_sect_schedule_view".
 *
 * @property int|null $teachers_load_id
 * @property int|null $subject_sect_studyplan_id
 * @property int|null $direction_id
 * @property int|null $teachers_id
 * @property float|null $week_time
 * @property int|null $subject_sect_id
 * @property string|null $studyplan_subject_list
 * @property int|null $plan_year
 * @property int|null $subject_sect_schedule_id
 * @property int|null $week_num
 * @property int|null $week_day
 * @property int|null $time_in
 * @property int|null $time_out
 * @property int|null $auditory_id
 * @property string|null $description
 */
class SubjectSectScheduleView extends \artsoft\db\ActiveRecord
{
    public $scheduleDisplay;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_sect_schedule_view';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
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
            [['teachers_load_id', 'subject_sect_studyplan_id', 'direction_id', 'teachers_id', 'subject_sect_id', 'plan_year', 'subject_sect_schedule_id', 'week_num', 'week_day', 'time_in', 'time_out', 'auditory_id'], 'integer'],
            [['week_time'], 'number'],
            [['scheduleDisplay'], 'safe'],
            [['studyplan_subject_list'], 'string'],
            [['description'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'teachers_load_id' => Yii::t('art/guide', 'Teachers Load'),
            'subject_sect_studyplan_id' => Yii::t('art/guide', 'Sect Name'),
            'direction_id' => Yii::t('art/teachers', 'Name Direction'),
            'teachers_id' => Yii::t('art/teachers', 'Teachers'),
            'week_time' => Yii::t('art/guide', 'Week Time'),
            'subject_sect_id' => Yii::t('art/guide', 'Subject Sect ID'),
            'studyplan_subject_list' => Yii::t('art/guide', 'Studyplan List'),
            'plan_year' => Yii::t('art/guide', 'Plan Year'),
            'subject_sect_schedule_id' => Yii::t('art/guide', 'Subject Sect Schedule'),
            'scheduleDisplay' => Yii::t('art/guide', 'Subject Sect Schedule'),
            'week_num' => Yii::t('art/guide', 'Week Num'),
            'week_day' => Yii::t('art/guide', 'Week Day'),
            'time_in' => Yii::t('art/guide', 'Time In'),
            'time_out' => Yii::t('art/guide', 'Time Out'),
            'auditory_id' => Yii::t('art/guide', 'Auditory'),
            'description' => Yii::t('art/guide', 'Description'),
        ];
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    public function getTeachersLoad()
    {
        return $this->hasOne(TeachersLoad::class, ['id' => 'teachers_load_id']);
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
     * @return \yii\db\ActiveQuery
     */
    public function getAuditory()
    {
        return $this->hasOne(Auditory::class, ['id' => 'auditory_id']);
    }

    /**
     * @return string
     */
    public function getScheduleDisplay()
    {
        $string = ' ' . ArtHelper::getWeekValue('short', $this->week_num);
        $string .= ' ' . ArtHelper::getWeekdayValue('short', $this->week_day) . ' ' . $this->time_in . '-' . $this->time_out;
        $string .= ' ' . $this->getItemScheduleNotice();
        return $this->time_in ? $string : null;
    }

    /**
     * В одной аудитории накладка по времени!
     * @param $model
     * @return \yii\db\ActiveQuery
     */
    public static function getScheduleOverLapping($model)
    {
        $thereIsAnOverlapping = self::find()->where(
            ['AND',
                ['!=', 'subject_sect_schedule_id', $model->id],
                ['auditory_id' => $model->auditory_id],
                ['direction_id' => $model->direction_id],
                ['plan_year' => RefBook::find('subject_sect_schedule_plan_year')->getValue($model->id)],
                ['OR',
                    ['AND',
                        ['<', 'time_in', $model->encodeTime($model->time_out)],
                        ['>=', 'time_in', $model->encodeTime($model->time_in)],
                    ],

                    ['AND',
                        ['<=', 'time_out', $model->encodeTime($model->time_out)],
                        ['>', 'time_out', $model->encodeTime($model->time_in)],
                    ],
                ],
                ['=', 'week_day', $model->week_day]
            ]);
        if ($model->getAttribute($model->week_num) !== null) {
            $thereIsAnOverlapping->andWhere(['=', 'week_num', $model->week_num]);
        }

        return $thereIsAnOverlapping;
    }

    /**
     * Преподаватель не может работать в одно и тоже время в разных аудиториях!
     * Концертмейстер не может работать в одно и тоже время в разных аудиториях!
     * @param $model
     * @return \yii\db\ActiveQuery
     */
    public static function getTeachersOverLapping($model)
    {
        $thereIsAnOverlapping = self::find()->where(
            ['AND',
                ['!=', 'subject_sect_schedule_id', $model->id],
                ['direction_id' => $model->direction_id],
                ['teachers_id' => $model->teachers_id],
                ['!=', 'auditory_id', $model->auditory_id],
                ['plan_year' => RefBook::find('subject_sect_schedule_plan_year')->getValue($model->id)],
                ['OR',
                    ['AND',
                        ['<', 'time_in', $model->encodeTime($model->time_out)],
                        ['>=', 'time_in', $model->encodeTime($model->time_in)],
                    ],

                    ['AND',
                        ['<=', 'time_out', $model->encodeTime($model->time_out)],
                        ['>', 'time_out', $model->encodeTime($model->time_in)],
                    ],
                ],
                ['=', 'week_day', $model->week_day]
            ]);
        if ($model->getAttribute($model->week_num) !== null) {
            $thereIsAnOverlapping->andWhere(['=', 'week_num', $model->week_num]);
        }

        return $thereIsAnOverlapping;
    }

    /**
     * Запрос на полное время занятий расписания преподавателя данной нагрузки
     * @return array|SubjectSectScheduleView|null|\yii\db\ActiveRecord
     */
    public function getTeachersOverLoad()
    {
        return self::find()
            ->select(new \yii\db\Expression('(SUM(time_out) - SUM(time_in)) as full_time, COUNT(teachers_load_id) as qty'))
            ->where(['=', 'teachers_load_id', $this->teachers_load_id])
            ->asArray()
            ->one();
    }

    /**
     * Проверка на суммарное время расписания = времени нагрузки
     * $delta_time - погрешность, в зависимости от кол-ва занятий
     * @return string|null
     * @throws \Exception
     */
    public function getTeachersOverLoadNotice()
    {
        $message = null;
        $delta_time = Yii::$app->settings->get('module.student_delta_time');
        $thereIsAnOverload = $this->getTeachersOverLoad();
        $weekTime = \artsoft\helpers\Schedule::academ2astr($this->week_time);
        if ($this->week_time != 0 && $thereIsAnOverload['full_time'] != null && abs(($weekTime - $thereIsAnOverload['full_time'])) > ($delta_time * $thereIsAnOverload['qty'])) {
            $message = 'Суммарное время в расписании занятий не соответствует нагрузке!';
        }
        return $message ? Tooltip::widget(['type' => 'warning', 'message' => $message]) : null;
    }

    /**
     * Проверка на необходимость добавления расписания
     * @return bool
     */
    public function getTeachersScheduleNeed()
    {
        $delta_time = Yii::$app->settings->get('module.student_delta_time');
        $thereIsAnOverload = $this->getTeachersOverLoad();
        $weekTime = \artsoft\helpers\Schedule::academ2astr($this->week_time);
        if ($this->week_time != 0 && $weekTime > $thereIsAnOverload['full_time'] && abs(($weekTime - $thereIsAnOverload['full_time'])) > ($delta_time * $thereIsAnOverload['qty'])) {
            return true;
        }
        return false;
    }

    /**
     * В одной аудитории накладка по времени!
     * Одновременное посещение разных дисциплин недопустимо!
     * Накладка по времени занятий концертмейстера!
     * Заданное расписание не соответствует планированию индивидуальных занятий!
     * Преподаватель не может работать в одно и тоже время в разных аудиториях!
     * Концертмейстер не может работать в одно и тоже время в разных аудиториях!
     *
     * @return null|string
     * @throws \Exception
     */
    public function getItemScheduleNotice()
    {
        $tooltip = [];
        if ($this->subject_sect_schedule_id) {
            $model = SubjectSectSchedule::findOne($this->subject_sect_schedule_id);
            if (self::getScheduleOverLapping($model)->exists() === true) {
                $info = [];
                foreach (self::getScheduleOverLapping($model)->all() as $itemModel) {
                    $info[] = RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id);
                }
                $message = 'В одной аудитории накладка по времени! ' . implode(', ', $info);
                Notice::registerDanger($message);
                $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
            }

            if (self::getTeachersOverLapping($model)->exists() === true) {
                $info = [];
                foreach (self::getScheduleOverLapping($model)->all() as $itemModel) {
                    $info[] = RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id);
                }
                $message = 'Преподаватель(концертмейстер) не может работать в одно и тоже время в разных аудиториях! ' . implode(', ', $info);
                Notice::registerDanger($message);
                $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
            }
            return implode('', $tooltip);
        }
        return null;
    }
}
