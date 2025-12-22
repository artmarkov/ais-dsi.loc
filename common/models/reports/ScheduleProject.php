<?php

namespace common\models\reports;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\Schedule;
use common\models\auditory\Auditory;
use common\models\education\EducationProgramm;
use common\models\studyplan\Studyplan;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ScheduleProject
{
    const template = 'document/schedule_project.xlsx';

    protected $plan_year;
    protected $auditories;
    protected $programs;
    protected $days;
    protected $count_flag;
    protected $name_flag;
    protected $subject_flag;
    protected $programm_name_flag;
    protected $programm_cat_flag;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->count_flag = $model_date->count_flag;
        $this->name_flag = $model_date->name_flag;
        $this->subject_flag = $model_date->subject_flag;
        $this->programm_name_flag = $model_date->programm_name_flag;
        $this->programm_cat_flag = $model_date->programm_cat_flag;
        $this->auditories = $this->getAuditories();
        $this->programs = $this->getPrograms();
        $this->days = ArtHelper::getWeekdayList();
        array_pop($this->days); // удаляем воскресение
//        echo '<pre>' . print_r($this->programs, true) . '</pre>';
//        die();
    }

    protected function getAuditories()
    {
        return ArrayHelper::index(Auditory::find()
            ->select('num, name, floor, area, capacity')
            ->where(['=', 'study_flag', true])
            ->asArray()
            ->orderBy('num')
            ->all(), 'num');
    }

    protected function getPrograms()
    {
        return ArrayHelper::index(EducationProgramm::find()
            ->joinWith('educationCat')
            ->select('education_programm.id as id, guide_education_cat.short_name as programm_name, guide_education_cat.programm_short_name as programm_cat')
            ->asArray()
            ->all(), 'id');
    }

    protected function getSchedules()
    {
        $models = (new Query())->from('subject_schedule_view')
            ->leftJoin('auditory', 'auditory.id = subject_schedule_view.auditory_id')
            ->select('auditory.num as auditory_num, week_day, time_in, time_out, sect_name, subject, programm_list, studyplan_subject_list')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['IS NOT', 'subject_schedule_id', NULL])
            ->andWhere(['OR',
                ['subject_schedule_view.status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['subject_schedule_view.status' => Studyplan::STATUS_INACTIVE],
                    ['subject_schedule_view.status_reason' => [1, 2, 4]]
                ]
            ])
            ->andWhere(['direction_id' => 1000])
            ->orderBy('week_day, time_in')
            ->all();

        return $models;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getData()
    {
        $items = [];
        $dataSchedules = ArrayHelper::index($this->getSchedules(), null, ['week_day', 'auditory_num']);

        $items[0] = [
            'rank' => 'day',
        ];
        foreach ($this->days as $weekDay => $nameDay) { // Инициация ячеек
            $items[0]['weekday_' . $weekDay] = $this->days[$weekDay] ?? $weekDay;
            foreach ($this->auditories as $auditoryNum => $value) {
                for ($item = 0; $item < 23; $item++) {
                    $items[0]['schedule_' . $weekDay . '_' . $auditoryNum . '_' . ($item + 1)] = '';
                }
            }
        }
        foreach ($dataSchedules as $weekDay => $auditorySchedule) {
            foreach ($auditorySchedule as $auditoryNum => $scheduleItem) {
                foreach ($scheduleItem as $item => $val) {
                    $items[0]['schedule_' . $weekDay . '_' . $auditoryNum . '_' . ($item + 1)] = $this->getTemplate($val);
                }
            }
        }

        return $items;
    }

    protected function getTemplate($val)
    {
        $string = Schedule::decodeTime($val['time_in']) . '-' . Schedule::decodeTime($val['time_out']);
        if ($this->name_flag) {
            $string .= ' ' . $val['sect_name'];
        }
        if ($this->subject_flag) {
            $string .= ' ' . $val['subject'];
        }
        if ($this->programm_name_flag) {
            $string .= ' ' . $this->getProgrammString($val['programm_list'], 'programm_name');
        }
        if ($this->programm_cat_flag) {
            $string .= ' ' . $this->getProgrammString($val['programm_list'], 'programm_cat');
        }
        if ($this->count_flag) {
            $string .= ' [' . count(explode(',', $val['studyplan_subject_list'])) . ' уч.]';
        }
        return $string;
    }

    protected function getProgrammString($programm_list, $field_name)
    {
        $data = [];
        foreach (explode(',', $programm_list) as $item => $id) {
            $data[] = $this->programs[$id][$field_name] ?? '';
        }
        rsort($data, 2);
        return implode(',', array_unique($data));
    }


    /**
     * @param $data
     * @return bool
     * @throws \yii\base\Exception
     */
    public
    function sendXlsx()
    {
        $data[0] = [
            'rank' => 'doc',
            'plan_year' => ArtHelper::getStudyYearsValue($this->plan_year),
        ];

        foreach ($this->auditories as $num => $value) {
            $data[0]['name_' . $num] = $value['num'] . '-' . $value['name'];
            $data[0]['area_' . $num] = $value['area'] . ' кв.м.';
            $data[0]['capacity_' . $num] = $value['capacity'] . ' чел.';
        }

        $output_file_name = str_replace('.', '_' . Yii::$app->formatter->asDate(time(), 'php:Y_m_d H_i') . '.', basename(self::template));
        $items = $this->getData();
//        echo '<pre>' . print_r($items, true) . '</pre>';
//        die();
        $tbs = DocTemplate::get(self::template)->setHandler(function ($tbs) use ($data, $items) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('day', $items);
            $tbs->PlugIn(OPENTBS_SELECT_SHEET, 2);
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('day', $items);
            $tbs->PlugIn(OPENTBS_SELECT_SHEET, 3);
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('day', $items);
            $tbs->PlugIn(OPENTBS_SELECT_SHEET, 4);
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('day', $items);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

}
