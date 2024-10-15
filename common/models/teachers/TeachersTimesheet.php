<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\own\Department;
use common\models\routine\Routine;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class TeachersTimesheet
{
    const template_timesheet_budget = 'document/tabel_teachers_budget.xlsx';
    const template_timesheet_extra_budget = 'document/tabel_teachers_extra_budget.xlsx';
    const template_timesheet_extra_budget_half = 'document/tabel_teachers_extra_budget_half.xlsx';

    const WORKDAY = 'Ф'; // рабочий день
    const VOCATION = 'О'; // Отпуск
    const DAYOFF = 'В'; // Выходной

    protected $is_lesson_mark = true; // учитывать выставление оценки
    protected $template_name;
    protected $timestamp_in;
    protected $timestamp_out;
    protected $is_avans;
    protected $progress_flag;
    protected $routine;
    protected $subject_type_id;
    protected $activity_list;
    protected $mon;
    protected $year;
    protected $plan_year;
    protected $activities;
    protected $teachers_list;
    protected $teachers_day_schedule;
    protected $teachers_day_consult;
    protected $teachers_day_schedule_total;
    protected $lesson_fact;

    public function __construct($model_date)
    {
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $this->timestamp_in = $timestamp[0];
        $this->timestamp_out = $timestamp[1];
        $this->mon = date('n', $this->timestamp_in);
        $this->year = date('Y', $this->timestamp_in);
        $this->plan_year = ArtHelper::getStudyYearDefault(null, $this->timestamp_in);
        $this->subject_type_id = $model_date->subject_type_id;
        $this->activity_list = $model_date->activity_list;
        $this->progress_flag = $model_date->progress_flag ?? false;
        $this->is_avans = $model_date->is_avans ?? false;
        $this->routine = $this->getRoutine();
        $this->activities = $this->getTeachersActivities();
        $this->teachers_list = $this->getTeachersList();
        $this->lesson_fact = $this->getLessonsFact();
        $this->teachers_day_schedule = $this->getTeachersDaySchedule();
        $this->teachers_day_schedule_total = $this->subject_type_id == 1000 ? $this->getTeachersDayScheduleTotal() : []; // для бюджета
        $this->teachers_day_consult = $this->getTeachersDayConsult();
        $this->template_name = $this->subject_type_id == 1000 ? self::template_timesheet_budget : ($this->is_avans ? self::template_timesheet_extra_budget_half : self::template_timesheet_extra_budget);
//        echo '<pre>' . print_r($this->lesson_fact, true) . '</pre>';
//        die();
    }

    protected function getRoutine()
    {
        $routine = [];
        $day_in = date('j', $this->timestamp_in);
        $day_out = !$this->is_avans ? date('j', $this->timestamp_out) : 15;

        for ($day = $day_in; $day <= $day_out; $day++) {
            $timestamp = mktime(12, 0, 0, $this->mon, $day, $this->year); // середина суток
            $isVocation = Routine::isVocation($timestamp);
            $isDayOff = Routine::isDayOff($timestamp);
            $routine[$day] = [
                'isVocation' => $isVocation,
                'isDayOff' => $isDayOff
            ];
        }
        return $routine;
    }

    protected function getLessonsFact()
    {
        $models = (new Query())->from('lesson_items')
            ->innerJoin('lesson_progress', 'lesson_progress.lesson_items_id = lesson_items.id')
            ->select(new \yii\db\Expression('lesson_date, subject_sect_studyplan_id, lesson_items.studyplan_subject_id, date_part(\'day\'::text, to_timestamp(lesson_date+10800)) AS day'))
            ->where(['between', 'lesson_date', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['IS NOT', 'lesson_mark_id', NULL])
            ->all();
        return ArrayHelper::index($models, null, ['day', 'subject_sect_studyplan_id', 'studyplan_subject_id']);
    }

    protected function getTeachersActivities()
    {
        $models = TeachersActivityView::find()
            ->where(['in', 'teachers_activity_id', $this->activity_list])
            ->orderBy('last_name, first_name, middle_name, direction_id, direction_vid_id')
            ->all();
        return $models;
    }

    protected function getTeachersList()
    {
        $teachers_list = ArrayHelper::getColumn($this->activities, 'teachers_id');
        return array_unique($teachers_list);
    }

    /**
     * Запрос на календарь занятий преподавателя
     * @param $teachersIds
     * @return array
     */
    protected function getTeachersDaySchedule()
    {
        $data_schedule = [];

        $models = (new Query())->from('activities_schedule_view')
            ->select(new \yii\db\Expression('direction_id, direction_vid_id, teachers_id, subject_sect_studyplan_id, studyplan_subject_id, (datetime_out-datetime_in) as time, date_part(\'day\'::text, to_timestamp(datetime_in+10800)) AS day'))
            ->where(['teachers_id' => $this->teachers_list])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->andWhere(['status' => 1])
            ->orderBy('datetime_in')
            ->all();
        foreach ($models as $item => $data) {
            $flag = ($this->progress_flag && isset($this->lesson_fact[$data['day']][$data['subject_sect_studyplan_id']][$data['studyplan_subject_id']])) || (!$this->progress_flag && $this->subject_type_id == 1001) || $this->subject_type_id == 1000;
            if ($flag) {
                $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['day']] = isset($data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['day']]) ? Schedule::astr2academ($data['time']) + $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['day']] : Schedule::astr2academ($data['time']);
            }
        }
        return $data_schedule;
    }

    /**
     * Для бюджете группируем  нагрузку за неделю
     * @return array
     */
    protected function getTeachersDayScheduleTotal()
    {
        $data_schedule_total = [];
        $models = (new Query())->from('teachers_load_view')
            ->select('direction_id, direction_vid_id, teachers_id, subject_type_id, SUM(load_time) as time')
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->andWhere(['status' => 1])
            ->groupBy('studyplan_subject_id, subject_sect_studyplan_id, direction_id, direction_vid_id, teachers_id, subject_type_id')
            ->all();

        foreach ($models as $item => $data) {
            $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']] = isset($data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']]) ? $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']] + $data['time'] * 4 : $data['time'] * 4;
        }

        return $data_schedule_total;
    }

    protected function getTeachersDayConsult()
    {
        $data_schedule = [];

        $models = (new Query())->from('consult_schedule_view')
            ->select(new \yii\db\Expression('direction_id, direction_vid_id, teachers_id, (datetime_out-datetime_in) as time, date_part(\'day\'::text, to_timestamp(datetime_in+10800)) AS day'))
            ->where(['teachers_id' => $this->teachers_list])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->andWhere(['status' => 1])
            ->orderBy('datetime_in')
            ->all();

        foreach ($models as $item => $data) {
            $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['day']] = isset($data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['day']]) ? Schedule::astr2academ($data['time']) + $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['day']] : Schedule::astr2academ($data['time']);
        }
        return $data_schedule;
//        echo '<pre>' . print_r($data_schedule, true) . '</pre>';
    }

    protected function getTeachersScheduleDay($day, $direction_id, $direction_vid_id, $teachers_id)
    {
        $full_time = 0;

        if (isset($this->teachers_day_schedule[$direction_id][$direction_vid_id][$teachers_id][$day])) {
            $full_time += $this->teachers_day_schedule[$direction_id][$direction_vid_id][$teachers_id][$day];
        }

        return $full_time > 0 ? $full_time : null;
    }

    protected function getTeachersConsultDay($day, $direction_id, $direction_vid_id, $teachers_id)
    {
        $full_time = 0;

        if (isset($this->teachers_day_consult[$direction_id][$direction_vid_id][$teachers_id][$day])) {
            $full_time += $this->teachers_day_consult[$direction_id][$direction_vid_id][$teachers_id][$day];
        }
        return $full_time > 0 ? $full_time : null;
    }

    protected function getTeachersDays($direction_id, $direction_vid_id, $teachers_id)
    {
        $data = [];
        $time_consult = null;
        for ($day = 1; $day <= 31; $day++) {
            $data['time'][$day] = $data['status'][$day] = null;
        }
        $time_consult_15 = $time_consult_30 = null;
        $day_in = date('j', $this->timestamp_in);
        $day_out = !$this->is_avans ? date('j', $this->timestamp_out) : 15;

        $data['time_total'] = $data['time_total_15'] = null;

        for ($day = $day_in; $day <= $day_out; $day++) {
            $time_consult = $this->getTeachersConsultDay($day, $direction_id, $direction_vid_id, $teachers_id);
            $time_consult_15 += $day <= 15 ? $time_consult : null;
            $time_consult_30 += $time_consult;

            $data['time'][$day] = $this->getTeachersScheduleDay($day, $direction_id, $direction_vid_id, $teachers_id) + $time_consult;
            $data['time'][$day] = $data['time'][$day] == 0 ? '' : $data['time'][$day];
            $data['status'][$day] = null;
            $isVocation = $this->routine[$day]['isVocation'];
            $isDayOff = $this->routine[$day]['isDayOff'];

            if ($isVocation) {
                $data['time'][$day] = null;
                $data['status'][$day] = self::VOCATION;
            } elseif ($isDayOff) {
                $data['time'][$day] = null;
                $data['status'][$day] = self::DAYOFF;
            } elseif (($data['time'][$day] > 0)) {
                $data['status'][$day] = self::WORKDAY;
            }
            $data['time_total'] = $this->teachers_day_schedule_total[$direction_id][$direction_vid_id][$teachers_id] ?? null;
            $data['time_total_15'] = ($data['time_total'] / 2) + $time_consult_15;
            $data['time_total'] = !$this->is_avans ? $data['time_total'] + $time_consult_30 : null;

        }
//        echo '<pre>' . print_r($data, true) . '</pre>';
        return $data;
    }

    /**
     * @param $departmentsIds
     * @return string
     */
    protected function getDepartmentsString($departmentsIds)
    {
        $departmentsIds = array_filter($departmentsIds, function ($value) {
            return !is_null($value) && $value !== '';
        });
        $array = Department::find()->select('name')->where(['id' => $departmentsIds])->orderBy('name')->column();
        return $array ? implode(', ', $array) : '';
    }

    /**
     * формирование документов: Табель учета пед часов
     *
     * @param $template
     * @throws \yii\base\InvalidConfigException
     */
    public function makeXlsx()
    {
        $data = [];
        $items = [];
        $department_list = [];
        $total['summ_qty_15'] = $total['summ_qty_31'] = $total['summ_time_15'] = $total['summ_time_31'] = 0;

        $userId = Yii::$app->user->identity->getId();
        $teachersId = RefBook::find('users_teachers')->getValue($userId);
        $teachersModel = Teachers::findOne(['id' => $teachersId]);

        foreach ($this->activities as $item => $d) {
            $department_list[] = $d->department_list;
            $mega = $this->getTeachersDays($d->direction_id, $d->direction_vid_id, $d->teachers_id);
            $items[] = [
                'rank' => 'a',
                'item' => $item + 1,
                'last_name' => $d->last_name,
                'first_name' => $d->first_name,
                'middle_name' => $d->middle_name,
                'stake_slug' => $d->stake_slug,
                'tab_num' => $d->tab_num,
                'direction_slug' => $d->direction_slug . ' - ' . $d->direction_vid_slug,
                'days' => $mega,
            ];

        }
        $departmentsIds = array_unique(explode(',', implode(',', $department_list)));

        $data[] = [
            'rank' => 'doc',
            'tabel_num' => $this->mon,
            'period_in' => date('j', $this->timestamp_in),
            'period_out' => !$this->is_avans ? date('j', $this->timestamp_out) : 15,
            'period_month' => ArtHelper::getMonthsList()[$this->mon],
            'period_year' => date('Y', $this->timestamp_in),
            'subject_type_name' => RefBook::find('subject_type_name')->getValue($this->subject_type_id),
            'org_briefname' => Yii::$app->settings->get('own.shortname'),
            'departments' => $this->getDepartmentsString($departmentsIds),
            'leader_iof' => Yii::$app->settings->get('own.head'),
            'employee_post' => isset($teachersModel->position) ? $teachersModel->position->name : '',
            'employee_iof' => RefBook::find('teachers_fio')->getValue($teachersId),
            'doc_data_mark' => Yii::$app->formatter->asDate(time(), 'php:d.m.Y'),
            'data_doc' => Yii::$app->formatter->asDate(time(), 'php:d.m.Y'),
            'doc_accountant_post' => 'Бухгалтер',
            'doc_accountant_iof' => Yii::$app->settings->get('own.chief_accountant_post'),
        ];
//        print_r($items); die();

        $output_file_name = Yii::$app->formatter->asDate(time(), 'php:Y-m-d_H-i-s') . '_' . basename($this->template_name);

        $tbs = DocTemplate::get($this->template_name)->setHandler(function ($tbs) use ($data, $items) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('a', $items);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }
}

