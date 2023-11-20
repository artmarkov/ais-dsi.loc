<?php

namespace common\models\schedule;

use artsoft\helpers\Schedule;
use artsoft\helpers\RefBook;
use artsoft\helpers\ArtHelper;
use artsoft\widgets\Notice;
use common\models\studyplan\Studyplan;
use common\models\teachers\TeachersPlan;
use Yii;
use artsoft\widgets\Tooltip;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject_schedule_view".
 *
 * @property int|null studyplan_subject_id
 * @property int|null subject_sect_studyplan_id
 * @property int|null studyplan_subject_list
 * @property int|null subject_type_id
 * @property int|null subject_sect_id
 * @property int|null plan_year
 * @property float|null week_time
 * @property int|null teachers_load_id
 * @property int|null direction_id
 * @property int|null teachers_id
 * @property int|null load_time
 * @property int|null subject_schedule_id
 * @property int|null week_num
 * @property int|null week_day
 * @property int|null time_in
 * @property int|null time_out
 * @property int|null auditory_id
 * @property string|null description
 * @property string|null sect_name
 * @property int|null status
 * @property int|null studyplan_id
 */
class SubjectScheduleView extends SubjectSchedule
{
    public $scheduleDisplay;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_schedule_view';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {

        $attr = parent::attributeLabels();
        $attr['studyplan_subject_id'] = Yii::t('art/guide', 'Subject Name');
        $attr['week_time'] = Yii::t('art/guide', 'Week Time');
        $attr['subject_sect_studyplan_id'] = Yii::t('art/guide', 'Sect Name');
        $attr['studyplan_subject_list'] = Yii::t('art/guide', 'Studyplan List');
        $attr['subject_sect_id'] = Yii::t('art/guide', 'Subject Sect ID');
        $attr['subject_type_id'] = Yii::t('art/guide', 'Subject Type ID');
        $attr['plan_year'] = Yii::t('art/studyplan', 'Plan Year');
        $attr['direction_id'] = Yii::t('art/teachers', 'Name Direction');
        $attr['teachers_id'] = Yii::t('art/teachers', 'Teachers');
        $attr['load_time'] = Yii::t('art/guide', 'Load Time');
        $attr['subject_schedule_id'] = Yii::t('art/guide', 'Subject Schedule');
        $attr['scheduleDisplay'] = Yii::t('art/guide', 'Subject Schedule');
        $attr['sect_name'] = Yii::t('art/guide', 'Sect Name');
        $attr['subject'] = Yii::t('art/guide', 'Subject');
        $attr['status'] = Yii::t('art/guide', 'Status');
        $attr['studyplan_id'] = Yii::t('art/guide', 'Studyplan');

        return $attr;
    }

//    /**
//     * В одной аудитории накладка по времени!
//     * Одновременное посещение разных дисциплин недопустимо!
//     * Накладка по времени занятий концертмейстера!
//     * Заданное расписание не соответствует планированию индивидуальных занятий!
//     * Преподаватель не может работать в одно и тоже время в разных аудиториях!
//     * Концертмейстер не может работать в одно и тоже время в разных аудиториях!
//     *
//     * @return null|string
//     * @throws \Exception
//     */
//    public function getItemScheduleNotice()
//    {
//        $tooltip = [];
//        if ($this->subject_schedule_id) {
//            $model = SubjectSchedule::findOne($this->subject_schedule_id);
//            if (self::getScheduleOverLapping($model)->exists() === true) {
//                $info = [];
//                foreach (self::getScheduleOverLapping($model)->all() as $itemModel) {
//                    $info[] = RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id);
//                }
//                $message = 'В одной аудитории накладка по времени! ' . implode(', ', $info);
//                //  Notice::registerDanger($message);
//                $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
//            }
//
//            if (self::getTeachersOverLapping($model)->exists() === true) {
//                $info = [];
//                foreach (self::getTeachersOverLapping($model)->all() as $itemModel) {
//                    $info[] = RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id);
//                }
//                $message = 'Преподаватель(концертмейстер) не может работать в одно и тоже время в разных аудиториях! ' . implode(', ', $info);
//                //   Notice::registerDanger($message);
//                $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
//            }
//            if ($this->direction_id === 1000 && $this->subject_sect_studyplan_id === 0 && self::getTeachersPlanScheduleOverLapping($model)->exists() === false) { // если занятия инд-е
//                $message = 'Заданное расписание не соответствует планированию индивидуальных занятий!';
//                $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
//            }
//
//            $model = SubjectScheduleStudyplanView::find()->where(['=', 'subject_schedule_id', $this->subject_schedule_id])->one();
//            if ($model) {
//                $studentsIds = SubjectScheduleStudyplanView::find()->select('student_id')
//                    ->where(['=', 'subject_schedule_id', $this->subject_schedule_id])
//                    ->andWhere(['=', 'status', Studyplan::STATUS_ACTIVE])->column();
//                if (self::getStudentScheduleOverLapping($model, $studentsIds)->exists() === true) {
//                    $info = [];
//                    foreach (self::getStudentScheduleOverLapping($model, $studentsIds)->all() as $itemModel) {
//                        $info[] = $itemModel->student_fio . '(' . $itemModel->sect_name . ' ' . $itemModel->subject . ')';
//                    }
//                    $message = 'Ученик не может в одно и то же время находиться в разных аудиториях! ' . implode(', ', $info);
//                    //  Notice::registerDanger($message);
//                    $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
//                }
//
//                return implode('', $tooltip);
//            }
//        }
//        return null;
//    }
//
//    /**
//     * В одной аудитории накладка по времени!
//     * @param $model
//     * @return \yii\db\ActiveQuery
//     */
//    public static function getScheduleOverLapping($model)
//    {
//        $thereIsAnOverlapping = SubjectScheduleView::find()->where(
//            ['AND',
//                ['!=', 'subject_schedule_id', $model->id],
//                ['auditory_id' => $model->auditory_id],
//                ['direction_id' => $model->directionId],
//                ['plan_year' => RefBook::find('subject_schedule_plan_year')->getValue($model->id)],
//                ['OR',
//                    ['AND',
//                        ['<', 'time_in', Schedule::encodeTime($model->time_out)],
//                        ['>', 'time_in', Schedule::encodeTime($model->time_in)],
//                    ],
//
//                    ['AND',
//                        ['<', 'time_out', Schedule::encodeTime($model->time_out)],
//                        ['>', 'time_out', Schedule::encodeTime($model->time_in)],
//                    ],
//                ],
//                ['=', 'week_day', $model->week_day]
//            ]);
//        if ($model->getAttribute($model->week_num) !== 0) {
//            $thereIsAnOverlapping->andWhere(['=', 'week_num', $model->week_num]);
//        }
//
//        return $thereIsAnOverlapping;
//    }
//
//    /**
//     * Преподаватель не может работать в одно и тоже время в разных аудиториях!
//     * Концертмейстер не может работать в одно и тоже время в разных аудиториях!
//     * @param $model
//     * @return \yii\db\ActiveQuery
//     */
//    public static function getTeachersOverLapping($model)
//    {
//        $thereIsAnOverlapping = SubjectScheduleView::find()->where(
//            ['AND',
//                ['!=', 'subject_schedule_id', $model->id],
//                ['direction_id' => $model->directionId],
//                ['teachers_id' => $model->teachersId],
//                ['!=', 'auditory_id', $model->auditory_id],
//                ['plan_year' => RefBook::find('subject_schedule_plan_year')->getValue($model->id)],
//                ['OR',
//                    ['AND',
//                        ['<', 'time_in', Schedule::encodeTime($model->time_out)],
//                        ['>=', 'time_in', Schedule::encodeTime($model->time_in)],
//                    ],
//
//                    ['AND',
//                        ['<=', 'time_out', Schedule::encodeTime($model->time_out)],
//                        ['>', 'time_out', Schedule::encodeTime($model->time_in)],
//                    ],
//                ],
//                ['=', 'week_day', $model->week_day]
//            ]);
//        if ($model->getAttribute($model->week_num) !== 0) {
//            $thereIsAnOverlapping->andWhere(['=', 'week_num', $model->week_num]);
//        }
//
//        return $thereIsAnOverlapping;
//    }
//
//    public static function getStudentScheduleOverLapping($model, $studentsIds)
//    {
//
//        $thereIsAnOverlapping = SubjectScheduleStudyplanView::find()->where(
//            ['AND',
//                ['!=', 'subject_schedule_id', $model->subject_schedule_id],
//                ['=', 'direction_id', $model->direction_id],
//                ['=', 'status', Studyplan::STATUS_ACTIVE],
//                ['student_id' => $studentsIds],
//                ['plan_year' => RefBook::find('subject_schedule_plan_year')->getValue($model->subject_schedule_id)],
//                ['OR',
//                    ['AND',
//                        ['<', 'time_in', Schedule::encodeTime($model->time_out)],
//                        ['>=', 'time_in', Schedule::encodeTime($model->time_in)],
//                    ],
//
//                    ['AND',
//                        ['<=', 'time_out', Schedule::encodeTime($model->time_out)],
//                        ['>', 'time_out', Schedule::encodeTime($model->time_in)],
//                    ],
//                ],
//                ['=', 'week_day', $model->week_day]
//            ]);
//        if ($model->getAttribute($model->week_num) !== 0) {
//            $thereIsAnOverlapping->andWhere(['=', 'week_num', $model->week_num]);
//        }
//
//        return $thereIsAnOverlapping;
//
//    }
//
//    /**
//     * Заданное расписание не соответствует планированию индивидуальных занятий!
//     * @param $model
//     * @return \yii\db\ActiveQuery
//     */
//    public static function getTeachersPlanScheduleOverLapping($model)
//    {
//        $thereIsAnOverlapping = TeachersPlan::find()
//            ->innerJoin('guide_teachers_direction', 'guide_teachers_direction.id = teachers_plan.direction_id')
//            ->where(
//                ['AND',
//                    ['is', 'parent', null],
//                    ['=', 'teachers_id', $model->teachersId],
//                    ['auditory_id' => $model->auditory_id],
//                    ['plan_year' => RefBook::find('subject_schedule_plan_year')->getValue($model->id)],
//                    ['AND',
//                        ['<=', 'time_plan_in', Schedule::encodeTime($model->time_in)],
//                        ['>=', 'time_plan_out', Schedule::encodeTime($model->time_out)],
//                    ],
//                    ['=', 'week_day', $model->week_day]
//                ])->andWhere(new \yii\db\Expression('CASE WHEN week_num != 0 THEN week_num = :week_num ELSE TRUE END', [':week_num' => $model->week_num]));;
//
//
//        return $thereIsAnOverlapping;
//    }

    /**
     * Запрос на полное время занятий расписания преподавателя данной нагрузки
     * @param $teachersLoadIds
     * @return array
     */
//    public static function getTeachersOverLoad($models)
//    {
//        $teachersLoadIds = \yii\helpers\ArrayHelper::getColumn($models, 'teachers_load_id');
//        $note = [];
//        $delta_time = Yii::$app->settings->get('module.student_delta_time');
//        $array = self::find()
//            ->select(new \yii\db\Expression('teachers_load_id, load_time, (SUM(time_out) - SUM(time_in)) as full_time, COUNT(teachers_load_id) as qty'))
//            ->where(['teachers_load_id' => $teachersLoadIds])
//            ->groupBy('teachers_load_id, load_time')
//            ->asArray()
//            ->all();
//        $array = ArrayHelper::index($array, 'teachers_load_id');
//        foreach ($array as $teachers_load_id => $data) {
//            $weekTime = Schedule::academ2astr($data['load_time']);
//            if ($data['load_time'] != 0 && $data['full_time'] != null && abs(($weekTime - $data['full_time'])) > ($delta_time * ($data['qty'] - $data['qty'] / 2))) {
//                $note[$teachers_load_id] = ['load_time' => $data['load_time'], 'full_time' => $data['full_time'], 'delta_time' => abs(($weekTime - $data['full_time'])/60)];
//            }
//        }
//        return $note;
//    }

    /**
     * Проверка на необходимость добавления расписания
     * @return bool
     */
//    public function getTeachersScheduleNeed($note, $teachers_load_id)
//    {
//        return isset($note[$teachers_load_id]);
//    }

    /**
     * Проверка на суммарное время расписания = времени нагрузки
     * $delta_time - погрешность, в зависимости от кол-ва занятий
     * @return string|null
     * @throws \Exception
     */
//    public function getTeachersOverLoadNotice($note, $teachers_load_id)
//    {
//        $message = null;
//        if ($this->getTeachersScheduleNeed($note, $teachers_load_id)) {
//            $message = 'Суммарное время в расписании занятий ' . Schedule::astr2academ($note[$teachers_load_id]['full_time']) . ' ак.час. не соответствует нагрузке ' . $note[$teachers_load_id]['load_time'] . ' ак.час и отличается на '. $note[$teachers_load_id]['delta_time'] . ' минут!';
//        }
//        return $message ? Tooltip::widget(['type' => 'warning', 'message' => $message]) : null;
//    }
//

//    public function getScheduleAccompLimit()
//    {
//        $thereIsAnAccompLimit = SubjectScheduleView::find()->where(
//            ['AND',
//                ['subject_sect_studyplan_id' => $this->subject_sect_studyplan_id],
//                ['direction_id' => $this->direction->parent],
//                ['auditory_id' => $this->auditory_id],
//                ['<=', 'time_in', Schedule::encodeTime($this->time_in)],
//                ['>=', 'time_out', Schedule::encodeTime($this->time_out)],
//                ['=', 'week_day', $this->week_day]
//            ]);
//        if ($this->getAttribute($this->week_num) !== 0) {
//            $thereIsAnAccompLimit->andWhere(['=', 'week_num', $this->week_num]);
//        }
//        return $thereIsAnAccompLimit;
//    }
//
//    public function getScheduleAccompLimitNotice()
//    {
//        if ($this->direction->parent != null) {
//            if ($this->subject_schedule_id) {
//                if ($this->getScheduleAccompLimit()->exists() === false) {
//                    $message = 'Концертмейстер может работать только в рамках расписания преподавателя';
////                $info = [];
////                $teachersSchedule = SubjectScheduleView::find()->where(
////                    ['AND',
////                        ['subject_sect_studyplan_id' => $this->subject_sect_studyplan_id],
////                        ['direction_id' => $this->direction->parent]
////                    ]);
////                foreach ($teachersSchedule->all() as $itemModel) {
////                    $string = ' ' . ArtHelper::getWeekValue('short', $itemModel->week_num);
////                    $string .= ' ' . ArtHelper::getWeekdayValue('short', $itemModel->week_day) . ' ' . $itemModel->time_in . '-' . $itemModel->time_out;
////                    $string .= ' ' . RefBook::find('auditory_memo_1')->getValue($itemModel->auditory_id);
////                    $info[] = $string;
////                }
//                    // $this->addError($attribute, $message);
//                    // Notice::registerWarning($message . ': ' . implode(', ', $info));
//                    return Tooltip::widget(['type' => 'danger', 'message' => $message]);
//                }
//            }
//        }
//    }

    public static function getScheduleIsExist($subject_sect_studyplan_id, $studyplan_subject_id)
    {
        if ($subject_sect_studyplan_id == 0) {
            return self::find()->where(['=', 'studyplan_subject_id', $studyplan_subject_id])->exists();

        }
        return self::find()->where(['=', 'subject_sect_studyplan_id', $subject_sect_studyplan_id])->exists();
    }

    /**
 * @return string|null
 * @throws \Exception
 */
    public function getScheduleDisplay()
    {
        $string = ' ' . ArtHelper::getWeekValue('short', $this->week_num);
        $string .= ' ' . ArtHelper::getWeekdayValue('short', $this->week_day) . ' ' . $this->time_in . '-' . $this->time_out;
        return $this->time_in ? $string : null;
    }

//    public function getScheduleNotice($note, $teachers_load_id)
//    {
//        $string = [];
//        $string[] = $this->getTeachersOverLoadNotice($note, $teachers_load_id);
////        $string[] = $this->getItemScheduleNotice();
////        $string[] = $this->getScheduleAccompLimitNotice();
//        return implode(' ', $string);
//    }

}
