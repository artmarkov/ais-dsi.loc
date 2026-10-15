<?php

namespace common\models\reports;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\Schedule;
use common\models\routine\Routine;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubjectHist;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class StudyplanTimesheet
{
    const template = 'document/studyplan_timesheet_2.xlsx';

    protected $plan_year;
    protected $timestamp_in;
    protected $timestamp_out;
    protected $days;
    protected $mon;
    protected $year;
    protected $education_cat_id;
    protected $programm_id;
    protected $subject_form_id;
    protected $studyplans;
    protected $studyplanIds;
    protected $holidays_flag = true; // каникулы
    protected $celebrations_flag = true; // праздники
    protected $routine;
    protected $data_day;
    protected $data_consult;

    public function __construct($model_date)
    {
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $this->timestamp_in = $timestamp[0];
        $this->timestamp_out = $timestamp[1];
        $this->mon = date('n', $this->timestamp_in);
        $this->year = date('Y', $this->timestamp_in);
        $this->plan_year = ArtHelper::getStudyYearDefault(9, $this->timestamp_in);
        $this->education_cat_id = $model_date->education_cat_id;
        $this->programm_id = $model_date->programm_id;
        $this->subject_form_id = $model_date->subject_form_id;
        $this->days = ArtHelper::getWeekdayList('short');
        $this->studyplans = $this->getStudyplans();
        $this->studyplanIds = ArrayHelper::getColumn($this->studyplans, 'id');
//        $this->routine = $this->getRoutine();
//        $this->data_day = $this->getStudyplanSchedules();
//        $this->data_consult = $this->getStudyplanDayConsult();
//        echo '<pre>' . print_r($this->getData(), true) . '</pre>';
//        die();
    }

    protected function getRoutine()
    {
        $routine = [];
        $day_in = date('j', $this->timestamp_in);
        $day_out = date('j', $this->timestamp_out);

        for ($day = $day_in; $day <= $day_out; $day++) {
            $timestamp = mktime(12, 0, 0, $this->mon, $day, $this->year); // середина суток
            $isHolidays = $this->holidays_flag ? Routine::isHolidays($timestamp) : false;
            $isCelebrations = $this->celebrations_flag ? Routine::isCelebrations($timestamp) : false;
            $routine[$day] = [
                'isBlock' => $isHolidays || $isCelebrations,
                'isHolidays' => $isHolidays,
                'isCelebrations' => $isCelebrations,
            ];
        }
        return $routine;
    }

    protected function getStudyplans()
    {
        $models = (new Query())->from('studyplan_view')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['<', 'doc_contract_start', $this->timestamp_out])
//            ->andWhere(['status' => Studyplan::STATUS_ACTIVE])
            ->andWhere(['programm_id' => $this->programm_id]);
        if ($this->subject_form_id) {
            $models = $models->andWhere(['subject_form_id' => $this->subject_form_id]);
        }
        $models = $models->orderBy('student_fio')->all();
        return $models;
    }

    protected function getStudyplanSchedules()
    {
        $data_schedule = (new Query())->from('subject_schedule_studyplan_view')
            ->select('studyplan_id, week_day, SUM(time_out-time_in) as time')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['IS NOT', 'subject_schedule_id', NULL])
            ->andWhere(['studyplan_id' => $this->studyplanIds])
            ->andWhere(['direction_id' => 1000])
            ->andWhere(['not in', 'studyplan_subject_id', StudyplanSubjectHist::getStudyplanSubjectPass($this->timestamp_in)])
            ->groupBy('studyplan_id, week_day')
            ->all();
        $data_schedule = ArrayHelper::index($data_schedule, 'week_day', ['studyplan_id']);
//        echo '<pre>' . print_r($data_schedule, true) . '</pre>'; die();
        return $data_schedule;
    }

    protected function getStudyplanDayConsult()
    {
        $data_schedule = (new Query())->from('consult_schedule_studyplan_view')
            ->select(new \yii\db\Expression('studyplan_id, SUM(datetime_out-datetime_in) as time, date_part(\'day\'::text, to_timestamp(datetime_in+10800)) AS day'))
            ->where(['studyplan_id' => $this->studyplanIds])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['not in', 'studyplan_subject_id', StudyplanSubjectHist::getStudyplanSubjectPass($this->timestamp_in)])
            ->groupBy('studyplan_id, day')
            ->all();
        $data_schedule = ArrayHelper::index($data_schedule, 'day', ['studyplan_id']);
//        echo '<pre>' . print_r($data_schedule, true) . '</pre>';die();
        return $data_schedule;
    }

    protected function getStudyplanDays($d)
    {
        $data = [];
        $day_out = date('j', $this->timestamp_out);
        $count_7 = $count_X = 0;

        for ($day = 1; $day <= 31; $day++) {
            $data['time'][$day] = null;
            $timestamp = mktime(0, 0, 0, $this->mon, $day, $this->year);
            $weekDay = date('w', $timestamp);
            $weekDay = $weekDay == 0 ? 7 : $weekDay;
            $doc_date = $d['doc_contract_start'];
//            $isBlock = $this->routine[$day]['isBlock'] ?? false;
//            $time_day = $this->data_day[$id][$weekDay]['time'] ?? 0;
//            $time_cons = $this->data_consult[$id][$day]['time'] ?? 0;
//            $time = (integer)$time_day + (integer)$time_cons;
//            $data['time'][$day] = Schedule::astr2academ($time);
//            if ($isBlock || $day > $day_out) {
//                $data['time'][$day] = null;
//            }
//            $data['time'][$day] = $data['time'][$day] == 0 ? '' : $data['time'][$day];
            if ($weekDay == 7 && $day <= $day_out) {
                $data['time'][$day] = 'В';
                $count_7++;
            }
            if ($doc_date != null && $data['time'][$day] == null) {
                $doc_day = date('d', $doc_date);
                $doc_mon = date('n', $doc_date);
                $doc_year = date('Y', $doc_date);

                if ($this->mon == $doc_mon && $this->year == $doc_year && $day < $doc_day) {
                    $data['time'][$day] = 'Х';
                    $count_X++;
                }
            }
        }
        $data['time']['total'] = $day_out - $count_7 - $count_X;
        $data['time']['x'] = $count_X != 0 ? $count_X : '';
        return $data;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getData()
    {
        $items = [];
        $items[0] = [
            'rank' => 'day',
        ];
        $day_out = date('j', $this->timestamp_out);
        for ($day = 1; $day <= 31; $day++) {
            $timestamp = mktime(0, 0, 0, $this->mon, $day, $this->year);
            $weekDay = date('w', $timestamp);
            $weekDay = $weekDay == 0 ? 7 : $weekDay;
            $items[0]['weekday'][$day] = $day > $day_out ? '' : $this->days[$weekDay];
            $items[0]['day'][$day] = $day > $day_out ? '' : $day;
        }

        return $items;
    }

    /**
     * @param $data
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendXlsx()
    {
        $data = $a = [];
        $data[] = [
            'rank' => 'doc',
            'period_in' => date('j', $this->timestamp_in),
            'period_out' => date('j', $this->timestamp_out),
            'period_month' => ArtHelper::getMonthsList()[$this->mon],
            'period_year' => date('Y', $this->timestamp_in)
        ];
//        echo '<pre>' . print_r($this->getStudyplans(), true) . '</pre>';die();
        foreach ($this->getStudyplans() as $item => $d) {
            $time = $this->getStudyplanDays($d);
            $a[] = [
                'rank' => 'a',
                'item' => $item + 1,
                'student_fio' => $d['student_fio'],
                'education_cat' => $d['education_cat_short_name'],
                'speciality' => $d['speciality'],
                'student_fls' => sprintf('%06d', $d['student_id']) . '-' . date('y', $d['doc_contract_start'])/* . '-' . $d['education_programm_short_name']*/,
                'cost_month_total' => $d['cost_month_total'],
                'days' => $time,
            ];

        }
        $output_file_name = str_replace('.', '_' . Yii::$app->formatter->asDate(time(), 'php:Y_m_d H_i') . '.', basename(self::template));
//        $items = $this->getData();
//        echo '<pre>' . print_r($items, true) . '</pre>';
//        die();
        $tbs = DocTemplate::get(self::template)->setHandler(function ($tbs) use ($data/*, $items*/, $a) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
//            $tbs->MergeBlock('day', $items);
            $tbs->MergeBlock('a', $a);


        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

}
