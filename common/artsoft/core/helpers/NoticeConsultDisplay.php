<?php

namespace artsoft\helpers;

use artsoft\widgets\Notice;
use common\models\schedule\ConsultScheduleView;
use yii\helpers\ArrayHelper;
use Yii;
use artsoft\widgets\Tooltip;

/**
 * Class NoticeConsultDisplay
 * @package artsoft\helpers
 *
 */
class NoticeConsultDisplay
{
    protected $models;
    protected $plan_year;
    protected $teachersLoadIds;
    protected $consultScheduleIds;
    protected $teachersLoadData;
    protected $scheduleOverLapping;
    protected $teachersOverLapping;
    protected $loadTimeConsultTotal;
    protected $yearTimeConsultTotal;
    protected $dateTimeTotal;

    public static function getData($models, $plan_year)
    {
        return new self($models, $plan_year);
    }

    public function __construct($models, $plan_year)
    {
        $this->models = $models;
        $this->plan_year = $plan_year;
        $this->teachersLoadIds = \yii\helpers\ArrayHelper::getColumn($this->models, 'teachers_load_id');
        $this->consultScheduleIds = array_filter(\yii\helpers\ArrayHelper::getColumn($this->models, 'consult_schedule_id'), function ($value) {
            return !is_null($value) && $value !== '';
        });
        $this->teachersLoadData = $this->getTeachersOverLoad(); // Запрос на полное время занятий расписания консультаций преподавателя
        $this->scheduleOverLapping = $this->getScheduleOverLapping($this->plan_year); // В одной аудитории накладка по времени!
        $this->teachersOverLapping = $this->getTeachersOverLapping($this->plan_year); // Преподаватель не может работать в одно и тоже время в разных аудиториях!
        $this->initTotal();
    }


    protected function initTotal()
    {
        $total0 = $total1 = $total2 = [];
        foreach ($this->models as $item) {
            if (!isset($total0[$item['teachers_load_id']])) {
                $total0[$item['teachers_load_id']] = $item['year_time_consult'];
            }
            if (!isset($total1[$item['teachers_load_id']])) {
                $total1[$item['teachers_load_id']] = $item['load_time_consult'];
            }
            if (isset($item['datetime_in']) && isset($item['datetime_out'])) {
                $total2[] = Schedule::astr2academ(\Yii::$app->formatter->asTimestamp($item['datetime_out']) - \Yii::$app->formatter->asTimestamp($item['datetime_in']));
            }
        }
        $this->yearTimeConsultTotal = array_sum($total0);
        $this->loadTimeConsultTotal = array_sum($total1);
        $this->dateTimeTotal = array_sum($total2);
    }

    /**
     * Запрос на полное время занятий расписания преподавателя данной нагрузки
     * @param $teachersLoadIds
     * @return array
     */
    public function getTeachersOverLoad()
    {
        $load_data = [];
        $array = ConsultScheduleView::find()
            ->select(new \yii\db\Expression('teachers_load_id, load_time_consult, (SUM(datetime_out) - SUM(datetime_in)) as full_time, COUNT(teachers_load_id) as qty'))
            ->where(['teachers_load_id' => $this->teachersLoadIds])
            ->groupBy('teachers_load_id, load_time_consult')
            ->asArray()
            ->all();
        $array = ArrayHelper::index($array, 'teachers_load_id');
        foreach ($array as $teachers_load_id => $data) {
            $weekTime = Schedule::academ2astr($data['load_time_consult']);
            if ($data['load_time_consult'] != 0 && $data['full_time'] != null && abs(($weekTime - $data['full_time'])) > 0) {
                $load_data[$teachers_load_id] = ['load_time_consult' => $data['load_time_consult'], 'full_time' => $data['full_time'], 'delta_time' => abs(($weekTime - $data['full_time']) / 60)];
            }
        }
        return $load_data;
    }

    /**
     * Проверка на необходимость добавления расписания консультаций
     * @return bool
     */
    public function getTeachersConsultScheduleNeed($model)
    {
        return isset($this->teachersLoadData[$model->teachers_load_id]) || !$model->consult_schedule_id;
    }

    /**
     * Проверка на суммарное время расписания = времени нагрузки
     * @param $model
     * @return string|null
     * @throws \Throwable
     */
    public function getTeachersConsultOverLoadNotice($model)
    {
        $message = null;
        if (isset($this->teachersLoadData[$model->teachers_load_id])) {
            $message = 'Суммарное время в расписании консультаций ' . Schedule::astr2academ($this->teachersLoadData[$model->teachers_load_id]['full_time']) . ' ак.час. не соответствует нагрузке ' . $this->teachersLoadData[$model->teachers_load_id]['load_time_consult'] . ' ак.час и отличается на ' . $this->teachersLoadData[$model->teachers_load_id]['delta_time'] . ' минут!';
        }
        return $message ? Tooltip::widget(['type' => 'warning', 'message' => $message]) : null;
    }

    /**
     * В одной аудитории накладка по времени!
     * Одновременное посещение разных дисциплин недопустимо!
     * Преподаватель не может работать в одно и тоже время в разных аудиториях!
     * Концертмейстер не может работать в одно и тоже время в разных аудиториях!
     *
     * @param $model
     * @return string|null
     * @throws \Throwable
     */
    public function getConsultOverLoopingNotice($model)
    {
        $tooltip = [];
        if (!$model->consult_schedule_id) {
            return null;
        }
        $models = $this->scheduleOverLapping;
        if (isset($models[$model->consult_schedule_id])) {
            $info = [];
            foreach ($models[$model->consult_schedule_id] as $index => $itemModel) {
                $info[] = $itemModel['sect_name'] . ' ' . Yii::$app->formatter->asDatetime($itemModel['datetime_in']) . '-' .  Yii::$app->formatter->asDatetime($itemModel['datetime_out']) . ' - ' . RefBook::find('auditory_memo_1')->getValue($itemModel['auditory_id']);
            }
            $message = 'В одной аудитории накладка по времени консультации! ' . implode(', ', $info);
//                  Notice::registerDanger($message);
            $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
        }

        $models = $this->teachersOverLapping;
        if (isset($models[$model->consult_schedule_id])) {
            $info = [];
            foreach ($models[$model->consult_schedule_id] as $index => $itemModel) {
                $info[] = $itemModel['sect_name'] . ' ' . RefBook::find('auditory_memo_1')->getValue($itemModel['auditory_id']);
            }
            $message = 'Преподаватель не может в одно и то же время проводить консультации в разных аудиториях! ' . implode(', ', $info);
            //   Notice::registerDanger($message);
            $tooltip[] = Tooltip::widget(['type' => 'danger', 'message' => $message]);
        }

        return implode('', $tooltip);
    }

    /**
     * В одной аудитории накладка по времени!
     * @return array
     * @throws \yii\db\Exception
     */
    public function getScheduleOverLapping($plan_year)
    {
        $thereIsAnOverlapping = \Yii::$app->db->createCommand('SELECT b.consult_schedule_id,
                              a.datetime_in, a.datetime_out, a.auditory_id, a.sect_name, a.subject
	                        FROM consult_schedule_view b, consult_schedule_view a 
	                        WHERE a.consult_schedule_id != b.consult_schedule_id
							AND a.direction_id = b.direction_id 
							AND a.auditory_id = b.auditory_id 
							AND a.status = 1
							AND b.status = 1
							AND a.plan_year = :plan_year
							AND b.plan_year = :plan_year
							AND ((a.datetime_in < b.datetime_out AND a.datetime_in >= b.datetime_in) OR (a.datetime_out <= b.datetime_out AND a.datetime_out > b.datetime_in))
							AND b.consult_schedule_id = ANY (string_to_array(:consult_schedule_ids, \',\')::int[])',
            [
                'plan_year' => $plan_year,
                'consult_schedule_ids' => implode(',', $this->consultScheduleIds),
            ])->queryAll();

        return $thereIsAnOverlapping ? ArrayHelper::index($thereIsAnOverlapping, null, ['consult_schedule_id']) : [];
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
        $thereIsAnOverlapping = \Yii::$app->db->createCommand('SELECT b.consult_schedule_id,
                              a.datetime_in, a.datetime_out, a.auditory_id, a.sect_name, a.subject
	                        FROM consult_schedule_view a, consult_schedule_view b 
	                        WHERE a.consult_schedule_id != b.consult_schedule_id
							AND a.auditory_id != b.auditory_id 
							AND a.direction_id = b.direction_id
							AND a.teachers_id = b.teachers_id
							AND a.plan_year = :plan_year
							AND b.plan_year = :plan_year
							AND ((a.datetime_in < b.datetime_out AND a.datetime_in >= b.datetime_in) OR (a.datetime_out < b.datetime_out AND a.datetime_out >= b.datetime_in))
							AND b.consult_schedule_id = ANY (string_to_array(:consult_schedule_ids, \',\')::int[])',
            [
                'plan_year' => $plan_year,
                'consult_schedule_ids' => implode(',', $this->consultScheduleIds),
            ])->queryAll();

        return $thereIsAnOverlapping ? ArrayHelper::index($thereIsAnOverlapping, null, ['consult_schedule_id']) : [];
    }

    /**
     * @return bool
     */
    public function confirmIsAvailable()
    {
        return !empty($this->models) && empty($this->teachersLoadData) && empty($this->scheduleOverLapping) && empty($this->teachersOverLapping)  && ($this->yearTimeConsultTotal == $this->dateTimeTotal);
    }

    /**
     * @param $fieldName
     * @return mixed
     */
    public function getTotal($fieldName)
    {
        if ($fieldName == 'load_time_consult') {
            return $this->loadTimeConsultTotal;
        } elseif ($fieldName == 'year_time_consult') {
            return $this->yearTimeConsultTotal;
        } elseif ($fieldName == 'datetime_in') {
            return $this->dateTimeTotal;
        }
    }
}