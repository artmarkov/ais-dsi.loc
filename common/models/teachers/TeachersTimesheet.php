<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\education\LessonItemsProgressView;
use common\models\own\Department;
use common\models\routine\Routine;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class TeachersTimesheet
{
    const template_timesheet = 'document/tabel_teachers.xlsx';

    const WORKDAY = 'Ф'; // рабочий день
    const VOCATION = 'О'; // Отпуск
    const DAYOFF = 'В'; // Выходной

    protected $timestamp_in;
    protected $timestamp_out;
    protected $is_avans;
    protected $routine;
    protected $subject_type_id;
    protected $activity_list;
    protected $teachers_list;
    protected $mon;
    protected $year;
    protected $plan_year;
    protected $teachers_day_schedule;
    protected $teachers_day_schedule_total;
    protected $teachers_day_consult;
    protected $teachers_day_consult_total;

    public function __construct($model_date)
    {
        $timestamp = ArtHelper::getMonYearParams($model_date->date_in);
        $this->timestamp_in = $timestamp[0];
        $this->timestamp_out = $timestamp[1];
        $this->mon = date('n', $this->timestamp_in);
        $this->year = date('Y', $this->timestamp_in);
        $this->plan_year = ArtHelper::getStudyYearDefault(null, $this->timestamp_in);
        $this->is_avans = $model_date->is_avans ?? false;
        $this->routine = $this->getRoutine();
        $this->subject_type_id = $model_date->subject_type_id;
        $this->activity_list = $model_date->activity_list;
        $this->teachers_list = $this->getTeachersList();
        $this->teachers_day_schedule_total = $this->getTeachersDayScheduleTotal();
        $this->teachers_day_schedule = $this->getTeachersDaySchedule();
        $this->getTeachersDayConsult();
//        print_r($model_date);
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

//    protected function getLessonsFact()
//    {
//        $models = LessonItemsProgressView::find()
//            ->where(['between', 'lesson_date', $this->timestamp_in, $this->timestamp_out])
//            ->where(['in', 'teachers_id', $this->teachers_list])
//            ->orderBy('lesson_date')
//            ->all();
//        return $models;
//    }

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
        $teachers_list = [];
        foreach ($this->getTeachersActivities() as $model) {
            $teachers_list[] = $model->teachers_id;
        }
        return array_unique($teachers_list);
    }

    protected function getTeachersDayFullTime($day, $direction_id, $direction_vid_id, $teachers_id)
    {
        $full_time = 0;
        $week_day = Schedule::getWeekDay($day, $this->mon, $this->year); // номер дня недели
        $week_num = Schedule::getWeekNum($day, $this->mon, $this->year);  // номер недели в месяце

        if (isset($this->teachers_day_schedule[$direction_id][$direction_vid_id][$teachers_id][$week_num][$week_day])) {
            $full_time += $this->teachers_day_schedule[$direction_id][$direction_vid_id][$teachers_id][$week_num][$week_day];
        }
        if (isset($this->teachers_day_schedule[$direction_id][$direction_vid_id][$teachers_id][0][$week_day])) {
            $full_time += $this->teachers_day_schedule[$direction_id][$direction_vid_id][$teachers_id][0][$week_day];
        }
        if (isset($this->teachers_day_consult[$direction_id][$direction_vid_id][$teachers_id][$day])) {
            $full_time += $this->teachers_day_consult[$direction_id][$direction_vid_id][$teachers_id][$day];
        }
        return $full_time > 0 ? $full_time : null;
    }

    protected function getTeachersDaySchedule()
    {
        $data_schedule = [];

        $models = (new Query())->from('subject_schedule_view')
            ->select('direction_id, direction_vid_id, teachers_id, week_day, week_num, time_in, time_out')
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->andWhere(['status' => 1])
            ->all();
        foreach ($models as $item => $data) {
            $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['week_num']][$data['week_day']] = isset($data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['week_num']][$data['week_day']]) ? $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['week_num']][$data['week_day']] + Schedule::astr2academ($data['time_out'] - $data['time_in']) : Schedule::astr2academ($data['time_out'] - $data['time_in']);
        }
        return $data_schedule;
    }

    protected function getTeachersDayScheduleTotal()
    {
        $data_schedule_total = [];

        if ($this->subject_type_id == 1000) { // Для бюджете группируем расписание по нагрузке
            $models_total = (new Query())->from('subject_schedule_view')
                ->select('direction_id, direction_vid_id, teachers_id, subject_type_id, SUM(time_out-time_in) as time')
                ->where(['in', 'teachers_id', $this->teachers_list])
                ->andWhere(['plan_year' => $this->plan_year])
                ->andWhere(['subject_type_id' => $this->subject_type_id])
                ->andWhere(['status' => 1])
                ->groupBy('studyplan_subject_id, subject_sect_studyplan_id, direction_id, direction_vid_id, teachers_id, subject_type_id')
                ->all();

            foreach ($models_total as $item => $data) {
                $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']] = isset($data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']]) ? $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']] + Schedule::astr2academ($data['time']) * 4 : Schedule::astr2academ($data['time']) * 4;
            }
        }
        return $data_schedule_total;
    }

    protected function getTeachersDayConsult()
    {
        $data_schedule = $data_schedule_total = [];
        $timestamp = mktime(0, 0, 0, $this->mon, 1, $this->year);

        for ($day = 1; $day <= date("t", $timestamp); $day++) {
            $timestamp_up = mktime(0, 0, 0, $this->mon, $day, $this->year); // начало суток
            $timestamp_end = mktime(23, 59, 59, $this->mon, $day, $this->year); // конец суток

            $models = (new Query())->from('consult_schedule_view')
                ->select(['direction_id', 'direction_vid_id', 'teachers_id', 'datetime_out', 'datetime_in'])
                ->where(['in', 'teachers_id', $this->teachers_list])
                ->andWhere(['subject_type_id' => $this->subject_type_id])
                ->andWhere(['between', 'datetime_in', $timestamp_up, $timestamp_end])
                ->andWhere(['status' => 1])
                ->all();
            foreach ($models as $item => $data) {
                $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$day] = isset($data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$day]) ? Schedule::astr2academ($data['datetime_out'] - $data['datetime_in']) + $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$day] : Schedule::astr2academ($data['datetime_out'] - $data['datetime_in']);
                $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']] = isset($data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']]) ? Schedule::astr2academ($data['datetime_out'] - $data['datetime_in']) + $data_schedule_total[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']] : Schedule::astr2academ($data['datetime_out'] - $data['datetime_in']);
            }
        }
        $this->teachers_day_consult = $data_schedule;
        $this->teachers_day_consult_total = $data_schedule_total;
//        echo '<pre>' . print_r($data_schedule, true) . '</pre>';
    }

    protected function getTeachersDays($direction_id, $direction_vid_id, $teachers_id)
    {
        $data = [];

        for ($day = 1; $day <= 31; $day++) {
            $data['time'][$day] = '';
            $data['status'][$day] = '';
        }
        $data['qty_15'] = $data['qty_31'] = $data['time_total'] = $data['time_total_15'] = null;

        $day_in = date('j', $this->timestamp_in);
        $day_out = !$this->is_avans ? date('j', $this->timestamp_out) : 15;

        for ($day = $day_in; $day <= $day_out; $day++) {
            $data['time'][$day] = $this->getTeachersDayFullTime($day, $direction_id, $direction_vid_id, $teachers_id);
            $isVocation = $this->routine[$day]['isVocation'];
            $isDayOff = $this->routine[$day]['isDayOff'];

            if ($isVocation) {
                $data['time'][$day] = null;
                $data['status'][$day] = self::VOCATION;
            } elseif ($isDayOff) {
                $data['status'][$day] = self::DAYOFF;
                $data['time'][$day] = null;
            }
            if ($data['time'][$day] > 0 && !$isVocation && !$isDayOff) {
                $data['status'][$day] = self::WORKDAY;
                if (!$this->is_avans) {
                    $data['time_total'] += $data['time'][$day];
                    $data['qty_31']++;
                }
                if ($day <= 15) {
                    $data['qty_15']++;
                    $data['time_total_15'] += $data['time'][$day];
                }
            }
        }
        if ($this->subject_type_id == 1000) {
            $data['time_total'] = 0;
            if (isset($this->teachers_day_schedule_total[$direction_id][$direction_vid_id][$teachers_id])) {
                $data['time_total'] += $this->teachers_day_schedule_total[$direction_id][$direction_vid_id][$teachers_id];
            }
            if (isset($this->teachers_day_consult_total[$direction_id][$direction_vid_id][$teachers_id])) {
                $data['time_total'] += $this->teachers_day_consult_total[$direction_id][$direction_vid_id][$teachers_id];
            }
            $data['time_total_15'] = $data['time_total'] / 2;
            $data['time_total'] = !$this->is_avans ? $data['time_total'] : null;
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

        foreach ($this->getTeachersActivities() as $item => $d) {
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

            $total['summ_time_15'] += $mega['time_total_15'];
            $total['summ_time_31'] += $mega['time_total'];
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
            'doc_accountant_iof' => 'Гвоздева Н.Д.',
            'summ_time_15' => $total['summ_time_15'],
            'summ_time_31' => $this->is_avans ? '' : $total['summ_time_31'],
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

    public function getTeachersCheetData()
    {
        $data = [];
        $directions = \Yii::$app->db->createCommand('SELECT 
                          concat(guide_teachers_direction.id, guide_teachers_direction_vid.id) as id,
                          concat(guide_teachers_direction.slug, guide_teachers_direction_vid.slug) as name
	            FROM guide_teachers_direction, guide_teachers_direction_vid;'
        )->queryAll();
//        print_r($directions); die();
        $directions = ArrayHelper::index($directions, 'id');

        $attributes = ['subject' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['sect_name' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['subject_type_id' => Yii::t('art/guide', 'Subject Type'),];
        $attributes += $directions;
        // Бюджет
        $models0 = (new Query())->from('subject_schedule_view')
            ->select('studyplan_subject_id, subject_sect_studyplan_id, direction_id, direction_vid_id, teachers_id, subject_type_id, SUM(time_out-time_in) as time')
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => 1000])
            ->andWhere(['status' => 1])
            ->groupBy('studyplan_subject_id, subject_sect_studyplan_id, direction_id, direction_vid_id, teachers_id, subject_type_id')
            ->all();

        $models0 = ArrayHelper::index($models0, null, ['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id']);

        // Внебюджет
        $models1 = (new Query())->from('activities_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'teachers_id', 'subject_type_id', 'datetime_in', 'datetime_out'])
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => 1001])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['status' => 1])
            ->all();

        $models1 = ArrayHelper::index($models1, null, ['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id']);

        $modelsConsult = (new Query())->from('consult_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'teachers_id', 'subject_type_id', 'datetime_in', 'datetime_out'])
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['between', 'datetime_in', $this->timestamp_in, $this->timestamp_out])
            ->andWhere(['status' => 1])
            ->all();
        $modelsConsult = ArrayHelper::index($modelsConsult, null, ['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id']);

        $modelsLoad = (new Query())->from('subject_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id', 'sect_name', 'subject'])
            ->distinct()
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['status' => 1])
            ->all();

        foreach ($modelsLoad as $i => $items) {
            $data[$i]['subject'] = $items['subject'];
            $data[$i]['sect_name'] = $items['sect_name'];
            $data[$i]['subject_type_id'] = $items['subject_type_id'];
            // согласно расписанию
            if ($items['subject_type_id'] == 1000) {
                if (isset($models0[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                    foreach ($models0[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']] as $k => $time) {
                        if (isset($data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'])) {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] += Schedule::astr2academ($time['time']) * 4;
                        } else {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] = Schedule::astr2academ($time['time']) * 4;
                        }
                    }
                }
            } else {
                if (isset($models1[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                    foreach ($models1[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']] as $k => $time) {
                        if (isset($data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'])) {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] += Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                        } else {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']]['teach'] = Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                        }
                    }
                }
            }
            // консультации
            if (isset($modelsConsult[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                $label = [];
                foreach ($modelsConsult[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']] as $k => $time) {
                    if (isset($data[$i][$items['direction_id'] . $items['direction_vid_id']]['cons'])) {
                        $data[$i][$items['direction_id'] . $items['direction_vid_id']]['cons'] += Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                    } else {
                        $data[$i][$items['direction_id'] . $items['direction_vid_id']]['cons'] = Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                    }
                        $label[] = Yii::$app->formatter->asDatetime($time['datetime_in']) . ' - ' . Yii::$app->formatter->asDatetime($time['datetime_out']);
                }
                $data[$i][$items['direction_id'] . $items['direction_vid_id']]['title'] = implode(',', $label);
            }
        }

//        echo '<pre>' . print_r(['data' => $modelsConsult, 'attributes' => $attributes, 'directions' => $directions], true) . '</pre>'; die();
        return ['data' => $data, 'attributes' => $attributes, 'directions' => $directions];
//        echo '<pre>' . print_r($this->teachers_day_consult, true) . '</pre>';
    }

    public static function getTotal($provider, $fieldName)
    {
        $total[0]['teach'] = $total[0]['cons'] = $total[1]['teach'] = $total[1]['cons'] = 0;
//        echo '<pre>' . print_r($provider, true) . '</pre>'; die();
        foreach ($provider as $item) {
            if ($item['subject_type_id'] == 1000) {
                $total[0]['teach'] += $item[$fieldName]['teach'] ?? 0;
                $total[0]['cons'] += $item[$fieldName]['cons'] ?? 0;
            } else {
                $total[1]['teach'] += $item[$fieldName]['teach'] ?? 0;
                $total[1]['cons'] += $item[$fieldName]['cons'] ?? 0;
            }
        }

        return $total[0]['teach'] . '/' . $total[0]['cons'] . '<span class="pull-right">' . $total[1]['teach'] . '/' . $total[1]['cons'] . '</span>';
    }
}

