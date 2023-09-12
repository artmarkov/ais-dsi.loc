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
    const template_timesheet = 'document/tabel_teachers.xlsx';

    const workday = 'Ф';
    const vocation = 'О';
    const dayoff = 'В';

    protected $date_in;
    protected $date_out;
    protected $subject_type_id;
    protected $activity_list;
    protected $teachers_list;
    protected $mon;
    protected $year;
    protected $plan_year;
    protected $teachers_day_schedule;
    protected $teachers_day_consult;

    public function __construct($model_date)
    {
        $this->date_in = $model_date->date_in;
        $this->date_out = $model_date->date_out;
        $this->subject_type_id = $model_date->subject_type_id;
        $this->activity_list = implode(',', $model_date->activity_list);
        $this->teachers_list = $this->getTeachersList();
        $this->mon = date('n', strtotime($this->date_in));
        $this->year = date('Y', strtotime($this->date_in));
        $this->plan_year = ArtHelper::getStudyYearDefault(null, strtotime($this->date_in));
        $this->teachers_day_schedule = $this->getTeachersDaySchedule($this->teachers_list);
        $this->teachers_day_consult = $this->getTeachersDayConsult($this->teachers_list);
    }


    protected function getTeachersActivities()
    {
        $models = TeachersActivityView::find()
            ->where(new \yii\db\Expression("teachers_activity_id = any(string_to_array('{$this->activity_list}', ',')::int[])"))
            ->orderBy('direction_id, direction_vid_id')
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

    protected function getTeachersDaySchedule($teachers_list)
    {
        $data_schedule = [];
        $models = (new Query())->from('subject_schedule_view')
            ->select('direction_id, direction_vid_id, teachers_id, week_day, week_num, time_in, time_out')
            ->where(['in', 'teachers_id', $teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->groupBy('direction_id, direction_vid_id, teachers_id, week_day, week_num, time_in, time_out')
            ->all();
        foreach ($models as $item => $data) {
            if (isset($data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['week_num']][$data['week_day']])) {
                $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['week_num']][$data['week_day']] += Schedule::astr2academ($data['time_out'] - $data['time_in']);
            } else {
                $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$data['week_num']][$data['week_day']] = Schedule::astr2academ($data['time_out'] - $data['time_in']);
            }
        }
        return $data_schedule;
    }

    protected function getTeachersDayConsult($teachers_list)
    {
        $data_schedule = [];
        $timestamp = mktime(0, 0, 0, $this->mon, 1, $this->year);

        for ($day = 1; $day <= date("t", $timestamp); $day++) {
            $timestamp_up = mktime(0, 0, 0, $this->mon, $day, $this->year); // начало суток
            $timestamp_end = mktime(23, 59, 59, $this->mon, $day, $this->year); // конец суток

            $models = (new Query())->from('consult_schedule_view')
                ->select(['direction_id', 'direction_vid_id', 'teachers_id', 'datetime_out', 'datetime_in'])
                ->where(['in', 'teachers_id', $teachers_list])
                ->andWhere(['subject_type_id' => $this->subject_type_id])
                ->andWhere(['and', ['>=', 'datetime_in', $timestamp_up], ['<=', 'datetime_in', $timestamp_end]])
                ->all();
            foreach ($models as $item => $data) {
                if (isset($data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$day])) {
                    $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$day] += Schedule::astr2academ($data['datetime_out'] - $data['datetime_in']);
                } else {
                    $data_schedule[$data['direction_id']][$data['direction_vid_id']][$data['teachers_id']][$day] = Schedule::astr2academ($data['datetime_out'] - $data['datetime_in']);
                }
            }
        }
//        echo '<pre>' . print_r($data_schedule, true) . '</pre>';
        return $data_schedule;
    }

    protected function getTeachersDays($direction_id, $direction_vid_id, $teachers_id)
    {
        $data = [];

        for ($day = 1; $day <= 31; $day++) {
            $data['time'][$day] = '';
            $data['status'][$day] = '';
        }
        $data['qty_15'] = $data['qty_31'] = $data['time_total'] = $data['time_total_15'] =  0;

        $day_in = date('j', strtotime($this->date_in));
        $day_out = date('j', strtotime($this->date_out));

        for ($day = $day_in; $day <= $day_out; $day++) {
            $week_day = Schedule::getWeekDay($day, $this->mon, $this->year); // номер дня недели
            $timestamp = mktime(12, 0, 0, $this->mon, $day, $this->year); // середина суток
            $data['time'][$day] = $this->getTeachersDayFullTime($day, $direction_id, $direction_vid_id, $teachers_id);
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
                    $data['time_total_15'] += $data['time'][$day];
                }
            }
        }

//        echo '<pre>' . print_r($data, true) . '</pre>';
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
            $total['summ_qty_15'] += $mega['qty_15'];
            $total['summ_qty_31'] += $mega['qty_31'];

            $total['summ_time_15'] += $mega['time_total_15'];
            $total['summ_time_31'] += $mega['time_total'];
        }
        $departmentsIds = array_unique(explode(',', implode(',', $department_list)));

        $data[] = [
            'rank' => 'doc',
            'tabel_num' => $this->mon,
            'period_in' => date('j', strtotime($this->date_in)),
            'period_out' => date('j', strtotime($this->date_out)),
            'period_month' => ArtHelper::getMonthsList()[$this->mon],
            'period_year' => date('Y', strtotime($this->date_in)),
//            'subject_type_name' => RefBook::find('subject_type_name')->getValue($this->subject_type_id),
            'org_briefname' => Yii::$app->settings->get('own.shortname'),
            'departments' => $this->getDepartmentsString($departmentsIds),
            'leader_iof' => Yii::$app->settings->get('own.head'),
            'employee_post' => isset($teachersModel->position) ? $teachersModel->position->name : '',
            'employee_iof' => RefBook::find('teachers_fio')->getValue($teachersId),
            'doc_data_mark' => Yii::$app->formatter->asDate(time(), 'php:d.m.Y'),
            'data_doc' => Yii::$app->formatter->asDate(time(), 'php:d.m.Y'),
            'doc_accountant_post' => 'Бухгалтер',
            'doc_accountant_iof' => 'Гвоздева Н.Д.',
            'summ_qty_15' => $total['summ_qty_15'],
            'summ_qty_31' => $total['summ_qty_31'],
            'summ_time_15' => $total['summ_time_15'],
            'summ_time_31' => $total['summ_time_31'],
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
        $timestamp_up = Yii::$app->formatter->asTimestamp($this->date_in);
        $timestamp_end = Yii::$app->formatter->asTimestamp($this->date_out) + 86399;
        $directions = \Yii::$app->db->createCommand('SELECT 
                          concat(guide_teachers_direction.id, guide_teachers_direction_vid.id) as id,
                          concat(guide_teachers_direction.slug, guide_teachers_direction_vid.slug) as name
	            FROM guide_teachers_direction, guide_teachers_direction_vid;'
        )->queryAll();

        $directions = ArrayHelper::index($directions, 'id');

        $attributes = ['subject' => Yii::t('art/guide', 'Subject Name')];
        $attributes += ['sect_name' => Yii::t('art/guide', 'Sect Name')];
        $attributes += ['subject_type_id' => Yii::t('art/guide', 'Subject Type'),];
        $attributes += $directions;
        // Бюджет
        $models0 = (new Query())->from('subject_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'teachers_id', 'subject_type_id', 'time_in', 'time_out'])
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->andWhere(['status' => 1])
            ->all();

        $models0 = ArrayHelper::index($models0, null, ['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id']);

        // Внебюджет
        $models1 = (new Query())->from('activities_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'teachers_id', 'subject_type_id', 'datetime_in', 'datetime_out'])
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->andWhere(['and', ['>=', 'datetime_in', $timestamp_up], ['<=', 'datetime_in', $timestamp_end]])
            ->andWhere(['status' => 1])
            ->all();

        $models1 = ArrayHelper::index($models1, null, ['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id']);

        $modelsConsult = (new Query())->from('consult_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'teachers_id', 'subject_type_id', 'datetime_in', 'datetime_out'])
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
            ->andWhere(['and', ['>=', 'datetime_in', $timestamp_up], ['<=', 'datetime_in', $timestamp_end]])
            ->andWhere(['status' => 1])
            ->all();
        $modelsConsult = ArrayHelper::index($modelsConsult, null, ['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id']);

        $modelsLoad = (new Query())->from('subject_schedule_view')
            ->select(['studyplan_subject_id', 'subject_sect_studyplan_id', 'direction_id', 'direction_vid_id', 'subject_type_id', 'sect_name', 'subject'])
            ->distinct()
            ->where(['in', 'teachers_id', $this->teachers_list])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['subject_type_id' => $this->subject_type_id])
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
                        if (isset($data[$i][$items['direction_id'] . $items['direction_vid_id']])) {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']] += Schedule::astr2academ($time['time_out'] - $time['time_in']) * 4;
                        } else {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']] = Schedule::astr2academ($time['time_out'] - $time['time_in']) * 4;
                        }
                    }
                }
            } else {
                if (isset($models1[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                    foreach ($models1[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']] as $k => $time) {
                        if (isset($data[$i][$items['direction_id'] . $items['direction_vid_id']])) {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']] += Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                        } else {
                            $data[$i][$items['direction_id'] . $items['direction_vid_id']] = Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                        }
                    }
                }
            }
            // консультации
            if (isset($modelsConsult[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']])) {
                foreach ($modelsConsult[$items['studyplan_subject_id']][$items['subject_sect_studyplan_id']][$items['direction_id']][$items['direction_vid_id']][$items['subject_type_id']] as $k => $time) {
                    if (isset($data[$i][$items['direction_id'] . $items['direction_vid_id']])) {
                        $data[$i][$items['direction_id'] . $items['direction_vid_id']] += Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                    } else {
                        $data[$i][$items['direction_id'] . $items['direction_vid_id']] = Schedule::astr2academ($time['datetime_out'] - $time['datetime_in']);
                    }
                }
            }
        }

//        echo '<pre>' . print_r(['data' => $data, 'attributes' => $attributes, 'directions' => $directions], true) . '</pre>'; die();
        return ['data' => $data, 'attributes' => $attributes, 'directions' => $directions];
//        echo '<pre>' . print_r($this->teachers_day_consult, true) . '</pre>';
    }

    public static function getTotal($provider, $fieldName)
    {
        $total = [0, 0];
//        echo '<pre>' . print_r($provider, true) . '</pre>'; die();
        foreach ($provider as $item) {
                if($item['subject_type_id'] == 1000) {
                    $total[0] += $item[$fieldName] ?? 0;
                } else {
                    $total[1] += $item[$fieldName] ?? 0;
                }
        }

        return $total[0] . '/' . $total[1];
    }
}

