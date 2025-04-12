<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use Yii;

class TeachersSchedule
{
    const template_timesheet = 'document/teachers_schedule.xlsx';

    protected $models;
    protected $modelTeachers;
    protected $plan_year;
    protected $teachers_id;

    public function __construct($models, $model_date, $modelTeachers)
    {
        $this->models = $models;
        $this->modelTeachers = $modelTeachers;
        $this->plan_year = $model_date->plan_year;
        $this->teachers_id = $model_date->teachers_id;
    }

    /**
     * формирование документов: Расписание преподавателя
     *
     * @param $template
     * @throws \yii\base\InvalidConfigException
     */
    public function makeXlsx()
    {
        $data[] = [
            'rank' => 'doc',
            'plan_year' => ArtHelper::getStudyYearsValue($this->plan_year),
            'teachers_fio' => RefBook::find('teachers_fio')->getValue($this->teachers_id),
        ];

        $dataSchedule['day'] = [];
        foreach ($this->models as $day => $value) {
            $dataSchedule['day'][$day] = [
                'day' => $day,
                'day_string' => \artsoft\helpers\ArtHelper::getWeekdayValue('name', $day),
            ];
            foreach ($value as $index => $items) {
                $time = Schedule::encodeTime($items->time_out) - Schedule::encodeTime($items->time_in);
                $time = Schedule::astr2academ($time);
                $dataSchedule['day'][$day]['items'][] = [
                    'index' => $index,
                    'time' => $items->time_in . ' - ' . $items->time_out,
                    'time_load' => $time,
                    'sect_name' => $items->sect_name,
                    'subject_type' => RefBook::find('subject_type_name_dev')->getValue($items->subject_type_id),
                    'subject' => $items->subject,
                    'direction' => \common\models\guidejob\Direction::getDirectionShortList()[$items->direction_id],
                    'auditory' => RefBook::find('auditory_memo_1')->getValue($items->auditory_id),
                ];
            }

        }
//        echo '<pre>' . print_r($dataSchedule, true) . '</pre>'; die();
        $output_file_name = str_replace('.', '_' . ArtHelper::slug(RefBook::find('teachers_fio')->getValue($this->teachers_id)) . '.' . Yii::$app->formatter->asDate(time(), 'php:Y_m_d H_i') . '.', basename(self::template_timesheet));

        $tbs = DocTemplate::get(self::template_timesheet)->setHandler(function ($tbs) use ($data, $dataSchedule) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('day', $dataSchedule['day']);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

}

