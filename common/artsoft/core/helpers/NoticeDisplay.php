<?php

namespace artsoft\helpers;

use artsoft\widgets\Notice;
use common\models\schedule\SubjectScheduleView;
use common\models\studyplan\Studyplan;
use yii\helpers\ArrayHelper;
use Yii;
use artsoft\widgets\Tooltip;

/**
 * Class NoticeDisplay
 * @package artsoft\helpers
 *
 */
class NoticeDisplay
{
    protected $models;
    protected $plan_year;
    protected $teachersLoadIds;
    protected $subjectScheduleIds;
    protected $teachersLoadData;
    protected $scheduleOverLapping;
    protected $teachersOverLapping;
    protected $teachersPlanScheduleOverLapping;
    protected $studentScheduleOverLapping;
    protected $scheduleAccompLimit;

    public static function getData($models, $plan_year)
    {
        return new self($models, $plan_year);
    }

    public function __construct($models, $plan_year)
    {
        $this->models = $models;
        $this->plan_year = $plan_year;
        $this->teachersLoadIds = array_unique(\yii\helpers\ArrayHelper::getColumn($this->models, 'teachers_load_id'));
        $this->subjectScheduleIds = array_filter(\yii\helpers\ArrayHelper::getColumn($this->models, 'subject_schedule_id'), function ($value) {
            return !is_null($value) && $value !== '';
        });
        $this->teachersLoadData = $this->getTeachersOverLoad(); // Запрос на полное время занятий расписания преподавателя данной нагрузки
        $this->scheduleOverLapping = $this->getScheduleOverLapping($this->plan_year); // В одной аудитории накладка по времени!
        $this->teachersOverLapping = $this->getTeachersOverLapping($this->plan_year); // Преподаватель не может работать в одно и тоже время в разных аудиториях!
        $this->teachersPlanScheduleOverLapping = $this->getTeachersPlanScheduleOverLapping($this->plan_year); // Заданное расписание не соответствует планированию индивидуальных занятий!
        $this->studentScheduleOverLapping = $this->getStudentScheduleOverLapping($this->plan_year); // Ученик не может в одно и то же время находиться в разных аудиториях!
        $this->scheduleAccompLimit = $this->getScheduleAccompLimit($this->plan_year); // Концертмейстер может работать только в рамках расписания преподавателя

    }

    /**
     * Запрос на полное время занятий расписания преподавателя данной нагрузки
     * @param $teachersLoadIds
     * @return array
     */
    public function getTeachersOverLoad()
    {
        $load_data = [];
        $delta_time = Yii::$app->settings->get('module.student_delta_time');
        $array = SubjectScheduleView::find()
            ->select(new \yii\db\Expression('teachers_load_id, load_time, (SUM(time_out) - SUM(time_in)) as full_time, COUNT(teachers_load_id) as qty'))
            ->where(['teachers_load_id' => $this->teachersLoadIds])
            ->groupBy('teachers_load_id, load_time')
            ->asArray()
            ->all();
        $array = ArrayHelper::index($array, 'teachers_load_id');
        foreach ($array as $teachers_load_id => $data) {
            $weekTime = Schedule::academ2astr($data['load_time']);
            if ($data['load_time'] != 0 && $data['full_time'] != null && abs(($weekTime - $data['full_time'])) > ($delta_time * ($data['qty'] - ($data['qty'] > 1 ? $data['qty'] / 2 : 0)))) {
                $load_data[$teachers_load_id] = ['load_time' => $data['load_time'], 'full_time' => $data['full_time'], 'delta_time' => abs(($weekTime - $data['full_time']) / 60)];
            }
        }
        return $load_data;

    }

    /**
     * Проверка на необходимость добавления расписания
     * @param $model
     * @return bool
     */
    public function getTeachersScheduleNeed($model)
    {
        return isset($this->teachersLoadData[$model->teachers_load_id]) || !$model->subject_schedule_id;
    }

    /**
     * Проверка на суммарное время расписания = времени нагрузки
     * $delta_time - погрешность, в зависимости от кол-ва занятий
     * @param $model
     * @return string|null
     * @throws \Throwable
     */
    public function getTeachersOverLoadNotice($model)
    {
        $message = null;
        if (isset($this->teachersLoadData[$model->teachers_load_id])) {
            $message = 'Суммарное время в расписании занятий ' . Schedule::astr2academ($this->teachersLoadData[$model->teachers_load_id]['full_time']) . ' ак.час. не соответствует нагрузке ' . $this->teachersLoadData[$model->teachers_load_id]['load_time'] . ' ак.час и отличается на ' . $this->teachersLoadData[$model->teachers_load_id]['delta_time'] . ' минут!';
        }
        return $message ? Tooltip::widget(['type' => 'warning', 'message' => $message]) : null;
    }

    /**
     * В одной аудитории накладка по времени!
     * Одновременное посещение разных дисциплин недопустимо!
     * Накладка по времени занятий концертмейстера!
     * Заданное расписание не соответствует планированию индивидуальных занятий!
     * Преподаватель не может работать в одно и тоже время в разных аудиториях!
     * Концертмейстер не может работать в одно и тоже время в разных аудиториях!
     * @param $model
     * @return string|null
     * @throws \Throwable
     */
    public function getItemScheduleNotice($model)
    {
        $tooltip = [];
        if (!$model->subject_schedule_id) {
            return null;
        }
        $models = $this->scheduleOverLapping;
        if (isset($models[$model->subject_schedule_id])) {
            $info = [];
            foreach ($models[$model->subject_schedule_id] as $index => $itemModel) {
//                print_r($models[$model->subject_schedule_id]); die();
                $info[] = $itemModel['sect_name'] . $this->getScheduleDisplay($itemModel);
            }

            $message = 'В одной аудитории накладка по времени! ' . implode(', ', $info);
//                  Notice::registerDanger($message);
            $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
        }

        $models = $this->teachersOverLapping;
        if (isset($models[$model->subject_schedule_id])) {
            $info = [];
            foreach ($models[$model->subject_schedule_id] as $index => $itemModel) {
                $info[] = $this->getScheduleDisplay($itemModel) . ' ' . RefBook::find('auditory_memo_1')->getValue($itemModel['auditory_id']);
            }
            $message = 'Преподаватель(концертмейстер) не может работать в одно и тоже время в разных аудиториях! ' . implode(', ', $info);
            //   Notice::registerDanger($message);
            $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
        }

        $models = $this->teachersPlanScheduleOverLapping;
        if (isset($models[$model->subject_schedule_id])) {
            $message = 'Заданное расписание не соответствует планированию индивидуальных занятий!';
            $tooltip[] = Tooltip::widget(['type' => 'warning', 'message' => $message]);
        }
//
        $models = $this->studentScheduleOverLapping;
        if (isset($models[$model->subject_schedule_id])) {
            $info = [];
            foreach ($models[$model->subject_schedule_id] as $index => $itemModel) {
                $info[] = $itemModel['student_fio'] . '(' . $itemModel['sect_name'] . ' ' . $itemModel['subject'] . ')';
            }
            $message = 'Ученик не может в одно и то же время находиться в разных аудиториях! ' . implode(', ', $info);
            //  Notice::registerDanger($message);
            $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
        }
//
        return implode('', $tooltip);
//            }

    }

    /**
     * В одной аудитории накладка по времени!
     * @return array
     * @throws \yii\db\Exception
     */
    public function getScheduleOverLapping($plan_year)
    {
        $thereIsAnOverlapping = \Yii::$app->db->createCommand('SELECT b.subject_schedule_id, a.week_num, a.week_day, 
                              a.time_in, a.time_out, a.auditory_id, a.sect_name, a.subject
	                        FROM subject_schedule_view b, subject_schedule_view a 
	                        WHERE a.subject_schedule_id != b.subject_schedule_id
							AND a.direction_id = b.direction_id 
							AND a.auditory_id = b.auditory_id 
							AND a.week_num = b.week_num
							AND a.week_day = b.week_day
							AND a.status = 1
							AND b.status = 1
							AND a.plan_year = :plan_year
							AND b.plan_year = :plan_year
							AND (((a.time_in < b.time_out AND a.time_in >= b.time_in) OR (a.time_out <= b.time_out AND a.time_out > b.time_in))
							AND ((b.time_in < a.time_out AND b.time_in >= a.time_in) OR (b.time_out <= a.time_out AND b.time_out > a.time_in)))
							AND b.subject_schedule_id = ANY (string_to_array(:subject_schedule_ids, \',\')::int[])',
            [
                'plan_year' => $plan_year,
                'subject_schedule_ids' => implode(',', $this->subjectScheduleIds),
            ])->queryAll();

        return $thereIsAnOverlapping ? ArrayHelper::index($thereIsAnOverlapping, null, ['subject_schedule_id']) : [];
    }


    /**
     * Преподаватель не может работать в одно и тоже время в разных аудиториях!
     * Концертмейстер не может работать в одно и тоже время в разных аудиториях!
     * @param $model
     * @return array|\yii\db\ActiveQuery
     * @throws \yii\db\Exception
     */
    public function getTeachersOverLapping($plan_year)
    {
        $thereIsAnOverlapping = \Yii::$app->db->createCommand('SELECT b.subject_schedule_id, a.week_num, a.week_day, 
                              a.time_in, a.time_out, a.auditory_id, a.sect_name, a.subject
	                        FROM subject_schedule_view a, subject_schedule_view b 
	                        WHERE a.subject_schedule_id != b.subject_schedule_id
							AND a.auditory_id != b.auditory_id 
							AND a.direction_id = b.direction_id
							AND a.teachers_id = b.teachers_id
							AND a.week_num = b.week_num
							AND a.week_day = b.week_day
							AND a.plan_year = :plan_year
							AND b.plan_year = :plan_year
							AND (((a.time_in < b.time_out AND a.time_in >= b.time_in) OR (a.time_out <= b.time_out AND a.time_out > b.time_in))
							OR ((b.time_in < a.time_out AND b.time_in >= a.time_in) OR (b.time_out <= a.time_out AND b.time_out > a.time_in)))
							AND b.subject_schedule_id = ANY (string_to_array(:subject_schedule_ids, \',\')::int[])',
            [
                'plan_year' => $plan_year,
                'subject_schedule_ids' => implode(',', $this->subjectScheduleIds),
            ])->queryAll();

        return $thereIsAnOverlapping ? ArrayHelper::index($thereIsAnOverlapping, null, ['subject_schedule_id']) : [];
    }

    /**
     * Заданное расписание не соответствует планированию индивидуальных занятий!
     * @param $model
     * @return array
     * @throws \yii\db\Exception
     */
    public function getTeachersPlanScheduleOverLapping($plan_year)
    {
        $thereIsAnOverlapping = \Yii::$app->db->createCommand('SELECT a.subject_schedule_id, b.week_num, b.week_day, 
                              b.time_plan_in, b.time_plan_out, b.auditory_id
	                        FROM teachers_plan b, subject_schedule_view a 
	                        WHERE a.auditory_id = b.auditory_id 
							AND a.direction_id = 1000
							AND a.subject_sect_studyplan_id = 0
							AND a.direction_id = b.direction_id
							AND a.teachers_id = b.teachers_id
							AND a.week_num = b.week_num
							AND a.week_day = b.week_day
							AND ((b.time_plan_in < a.time_out AND b.time_plan_in > a.time_in) OR (b.time_plan_out < a.time_out AND b.time_plan_out > a.time_in))
							AND a.plan_year = :plan_year
							AND b.plan_year = :plan_year
							AND a.subject_schedule_id = ANY (string_to_array(:subject_schedule_ids, \',\')::int[])',
            [
                'plan_year' => $plan_year,
                'subject_schedule_ids' => implode(',', $this->subjectScheduleIds),
            ])->queryAll();

        return $thereIsAnOverlapping ? ArrayHelper::index($thereIsAnOverlapping, null, ['subject_schedule_id']) : [];
    }

    /**
     * Ученик не может в одно и то же время находиться в разных аудиториях!
     * @param $plan_year
     * @return array
     * @throws \yii\db\Exception
     */
    public function getStudentScheduleOverLapping($plan_year)
    {
        $thereIsAnOverlapping = \Yii::$app->db->createCommand('SELECT b.subject_schedule_id, a.week_num, a.week_day, 
                              a.time_in, a.time_out, a.auditory_id, a.sect_name, a.subject, a.student_fio
	                        FROM subject_schedule_studyplan_view a, subject_schedule_studyplan_view b 
	                        WHERE a.subject_schedule_id != b.subject_schedule_id
							AND a.auditory_id != b.auditory_id 
							AND a.direction_id = b.direction_id
							AND a.week_num = b.week_num
							AND a.week_day = b.week_day
							AND a.plan_year = :plan_year
							AND b.plan_year = :plan_year
							AND a.status = :status
							AND b.status = :status
							AND a.student_id = b.student_id
							AND (((a.time_in < b.time_out AND a.time_in >= b.time_in) OR (a.time_out <= b.time_out AND a.time_out > b.time_in))
							OR ((b.time_in < a.time_out AND b.time_in >= a.time_in) OR (b.time_out <= a.time_out AND b.time_out > a.time_in)))
							AND b.subject_schedule_id = ANY (string_to_array(:subject_schedule_ids, \',\')::int[])',
            [
                'plan_year' => $plan_year,
                'status' => 1,
                'subject_schedule_ids' => implode(',', $this->subjectScheduleIds),
            ])->queryAll();

        return $thereIsAnOverlapping ? ArrayHelper::index($thereIsAnOverlapping, null, ['subject_schedule_id']) : [];

    }

    /**
     * @param $plan_year
     * @return array
     * @throws \yii\db\Exception
     */
    public function getScheduleAccompLimit($plan_year)
    {
        $thereIsAnAccompLimit = \Yii::$app->db->createCommand('SELECT a.subject_schedule_id, b.week_num, b.week_day, 
                              b.time_in, b.time_out, b.auditory_id, b.sect_name, b.subject
	                        FROM subject_schedule_view a, subject_schedule_view b 
	                        WHERE  a.studyplan_subject_id = b.studyplan_subject_id
							AND a.subject_sect_studyplan_id = b.subject_sect_studyplan_id
							AND a.auditory_id = b.auditory_id 
							AND a.direction_id = 1001
							AND b.direction_id = 1000
							AND a.week_num = b.week_num
							AND a.week_day = b.week_day
							AND a.plan_year = :plan_year
							AND b.plan_year = :plan_year
							AND ((a.time_in >= b.time_in AND a.time_out <= b.time_out))
							AND b.subject_schedule_id = ANY (string_to_array(:subject_schedule_ids, \',\')::int[])',
            [
                'plan_year' => $plan_year,
                'subject_schedule_ids' => implode(',', $this->subjectScheduleIds),
            ])->queryAll();

        return $thereIsAnAccompLimit ? ArrayHelper::index($thereIsAnAccompLimit, null, ['subject_schedule_id']) : [];
    }

    /**
     * @param $model
     * @return string
     * @throws \Throwable
     */
    public function getScheduleAccompLimitNotice($model)
    {
        $models = $this->scheduleAccompLimit;

        if ($model->direction->parent != null && $model->subject_schedule_id != null) {
            if (!isset($models[$model->subject_schedule_id])) {
                $message = 'Концертмейстер может работать только в рамках расписания преподавателя!';

                return Tooltip::widget(['type' => 'danger', 'message' => $message]);
            }
        }
    }

    /**
     * @param $model
     * @return string
     * @throws \Throwable
     */
    public function getScheduleNotice($model)
    {
        $string = [];
        $string[] = $this->getTeachersOverLoadNotice($model);
        $string[] = $this->getItemScheduleNotice($model);
        $string[] = $this->getScheduleAccompLimitNotice($model);
        return implode('', $string);
    }

    public function confirmIsAvailable()
    {
        $subjectScheduleNull = array_filter(\yii\helpers\ArrayHelper::getColumn($this->models, 'subject_schedule_id'), function ($value) {
            return is_null($value) && $value == '';
        });
        $subjectScheduleNull = array_diff($subjectScheduleNull, array('', null));
        $subjectScheduleAccomp = array_filter($this->models, function ($value) {
            return $value['direction_id']  == 1001 && !is_null($value['subject_schedule_id']);
        });
        $subjectScheduleAccomp = \yii\helpers\ArrayHelper::getColumn($subjectScheduleAccomp, 'subject_schedule_id');
        $subjectScheduleAccomp = array_diff($subjectScheduleAccomp, array_keys($this->scheduleAccompLimit));
     //   echo '<pre>' . print_r($subjectScheduleNull, true) . '</pre>'; die();
        return !empty($this->models) && empty($this->teachersLoadData) /*&& empty($this->scheduleOverLapping)*/ && empty($this->teachersOverLapping) && empty($this->teachersPlanScheduleOverLapping) /*&& empty($this->studentScheduleOverLapping) */&& empty($subjectScheduleAccomp) && empty($subjectScheduleNull);
    }

    public function getScheduleDisplay($model)
    {
        $string = [];
        $string[] = $model['week_num'] != 0 ? \artsoft\helpers\ArtHelper::getWeekValue('short', $model['week_num']) : null;
        $string[] = \artsoft\helpers\ArtHelper::getWeekdayValue('short', $model['week_day']) . ' ' . \artsoft\helpers\Schedule::decodeTime($model['time_in']) . '-' . \artsoft\helpers\Schedule::decodeTime($model['time_out']);
        return implode(' ', $string);
    }

}