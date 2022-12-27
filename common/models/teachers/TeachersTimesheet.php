<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\own\Department;
use common\models\routine\Routine;
use common\models\schedule\ConsultScheduleView;
use common\models\schedule\SubjectScheduleView;
use Yii;

class TeachersTimesheet
{
    const template_timesheet = 'document/tabel_teachers.xlsx';

    const workday = 'Ф';
    const vocation = 'О';
    const dayoff = 'В';

    protected $date_in;
    protected $date_out;
    protected $subject_type_id;
    protected $activity_list;
    protected $mon;
    protected $year;
    protected $plan_year;

    public function __construct($model_date)
    {
        $this->date_in = $model_date->date_in;
        $this->date_out = $model_date->date_out;
        $this->subject_type_id = $model_date->subject_type_id;
        $this->activity_list = implode(',', $model_date->activity_list);
        $this->mon = date('n', strtotime($this->date_in));
        $this->year = date('Y', strtotime($this->date_in));
        $this->plan_year = ArtHelper::getStudyYearDefault(null, strtotime($this->date_in));
    }

    protected function getTeachersActivities()
    {
        return TeachersActivityView::find()
            ->where(new \yii\db\Expression("teachers_activity_id = any(string_to_array('{$this->activity_list}', ',')::int[])"))
            ->all();
    }

    protected function getTeachersDayFullTime($day, $direction_id, $teachers_id)
    {
        $full_time = $this->getTeachersDaySchedule($day, $direction_id, $teachers_id);
        $full_time += $this->getTeachersDayConsult($day, $direction_id, $teachers_id);

        return $full_time > 0 ? $full_time : null;
    }

    protected function getTeachersDaySchedule($day, $direction_id, $teachers_id)
    {
        $week_day = Schedule::getWeekDay($day, $this->mon, $this->year); // номер дня недели
        $week_num = Schedule::getWeekNum($day, $this->mon, $this->year);  // номер недели в месяце

        $full_time = SubjectScheduleView::find()
            ->select(new \yii\db\Expression("(SUM(time_out) - SUM(time_in)) as full_time"))
            ->where(['direction_id' => $direction_id])
            ->andWhere(['teachers_id' => $teachers_id])
            ->andWhere(['week_day' => $week_day])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->andWhere(new \yii\db\Expression("case when week_num is not null then week_num = {$week_num} else week_num is null end"))
            ->scalar();

        return Schedule::astr2academ($full_time);
    }

    protected function getTeachersDayConsult($day, $direction_id, $teachers_id)
    {
        $timestamp_up = mktime(0, 0, 0, $this->mon, $day, $this->year); // начало суток
        $timestamp_end = mktime(23, 59, 59, $this->mon, $day, $this->year); // конец суток

        $full_time = ConsultScheduleView::find()
            ->select(new \yii\db\Expression("(SUM(datetime_out) - SUM(datetime_in)) as full_time"))
            ->where(['direction_id' => $direction_id])
            ->andWhere(['teachers_id' => $teachers_id])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->andWhere(['and', ['>=', 'datetime_in', $timestamp_up], ['<=', 'datetime_in', $timestamp_end]])
            ->scalar();

        return Schedule::astr2academ($full_time);
    }

    protected function getTeachersDays($direction_id, $teachers_id)
    {
        for ($day = 1; $day <= 31; $day++) {
            $data['time'][$day] = '';
            $data['status'][$day] = '';
        }
        $data['qty_15'] = $data['qty_31'] = $data['time_total'] = 0;
        $day_in = date('j', strtotime($this->date_in));
        $day_out = date('j', strtotime($this->date_out));

        for ($day = $day_in; $day <= $day_out; $day++) {
            $week_day = Schedule::getWeekDay($day, $this->mon, $this->year); // номер дня недели
            $timestamp = mktime(12, 0, 0, $this->mon, $day, $this->year); // середина суток
            $data['time'][$day] = $this->getTeachersDayFullTime($day, $direction_id, $teachers_id);
            $isVocation = Routine::isVocation($timestamp) ? true : false;
            $isDayOff = Routine::isDayOff($timestamp) || $week_day == 7 ? true : false;

            if ($isVocation) {
                $data['time'][$day] = null;
                $data['status'][$day] = self::vocation;
            } elseif ($isDayOff) {
                $data['status'][$day] = self::dayoff;
                $data['time'][$day] = null;
            }
            if ($data['time'][$day] > 0 && !$isVocation && !$isDayOff) {
                $data['status'][$day] = self::workday;
                $data['time_total'] += $data['time'][$day];
                $data['qty_31']++;
                if ($day <= 15) {
                    $data['qty_15']++;
                }
            }
        }
        return $data;
    }

    protected function getDepartmentsString($departmentsIds)
    {
        $v = [];
        foreach ($departmentsIds as $id) {
            if (!$id) {
                continue;
            }
            $v[] = Department::findOne($id)->name;
        }
        return implode(', ', $v);
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

        $userId = Yii::$app->user->identity->getId();
        $teachersId = RefBook::find('users_teachers')->getValue($userId);
        $teachersModel = Teachers::findOne(['id' => $teachersId]);

        foreach ($this->getTeachersActivities() as $item => $d) {
            $department_list[] = $d->department_list;
            $items[] = [
                'rank' => 'a',
                'item' => $item + 1,
                'last_name' => $d->last_name,
                'first_name' => $d->first_name,
                'middle_name' => $d->middle_name,
                'stake_slug' => $d->stake_slug,
                'tab_num' => $d->tab_num,
                'direction_slug' => $d->direction_slug,
                'days' => $this->getTeachersDays($d->direction_id, $d->teachers_id),
            ];
        }
        $departmentsIds = array_unique(explode(',', implode(',', $department_list)));

        $data[] = [
            'rank' => 'doc',
            'tabel_num' => $this->mon,
            'period_in' => date('j', strtotime($this->date_in)),
            'period_out' => date('j', strtotime($this->date_out)),
            'period_month' => ArtHelper::getMonthsList()[$this->mon],
            'period_year' => date('Y', strtotime($this->date_in)),
            'subject_type_name' => RefBook::find('subject_type_name')->getValue($this->subject_type_id),
            'org_briefname' => Yii::$app->settings->get('own.shortname'),
            'departments' => $this->getDepartmentsString($departmentsIds),
            'leader_iof' => Yii::$app->settings->get('own.head'),
            'employee_post' => isset($teachersModel->position) ? $teachersModel->position->name : '',
            'employee_iof' => RefBook::find('teachers_fio')->getValue($teachersId),
            'doc_data_mark' => Yii::$app->formatter->asDate(time(), 'php:d.m.Y'),
            'data_doc' => Yii::$app->formatter->asDate(time(), 'php:d.m.Y'),
        ];
//        print_r($items); die();

        $output_file_name = str_replace('.', '_' . Yii::$app->formatter->asDate(time(), 'php:Y_m_d') . '_' . '.', basename(self::template_timesheet));

        $tbs = DocTemplate::get(self::template_timesheet)->setHandler(function ($tbs) use ($data, $items) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('a', $items);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }
}
