<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\schedule\ConsultScheduleConfirm;
use common\models\schedule\ConsultScheduleView;
use Yii;

class TeachersConsult
{
    const template_timesheet = 'document/teachers_consult.xlsx';

    protected $models;
    protected $modelTeachers;
    protected $plan_year;
    protected $teachers_id;
    protected $modelConfirm;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->teachers_id = $model_date->teachers_id;
        $this->modelTeachers = Teachers::findOne($this->teachers_id);
        $this->modelConfirm = $this->getConfirmData();
    }

    public function getData()
    {
        $data = ConsultScheduleView::find()
            ->where(['=', 'teachers_id', $this->teachers_id])
            ->andWhere(['=', 'plan_year', $this->plan_year])
            ->andWhere(['IS NOT', 'consult_schedule_id', NULL])
            ->all();
        $this->models = $data;
        return $this->models;
    }

    protected function getConfirmData()
    {

        return ConsultScheduleConfirm::find()
            ->where(['=', 'teachers_id', $this->teachers_id])
            ->andWhere(['=', 'plan_year', $this->plan_year])
            ->one();
    }

    /**
     * формирование документов: Расписание консультаций преподавателя
     *
     * @param $template
     * @throws \yii\base\InvalidConfigException
     */
    public function makeXlsx()
    {
        $auditory_list = RefBook::find('auditory_memo_1')->getList();
        $direction_list = \common\models\guidejob\Direction::getDirectionShortList();

        $data[] = [
            'rank' => 'doc',
            'plan_year' => ArtHelper::getStudyYearsValue($this->plan_year),
            'teachers_fio' => RefBook::find('teachers_fio')->getValue($this->teachers_id),
            'signer_fio' => $this->modelConfirm ? RefBook::find('teachers_fio')->getValue($this->modelConfirm->teachers_sign) : '',
            'date_sign' => $this->modelConfirm ? Yii::$app->formatter->asDate($this->modelConfirm->updated_at) : '',
        ];

        $dataSchedule = [];
        //echo '<pre>' . print_r($this->getData(), true) . '</pre>'; die();

        foreach ($this->models ?? $this->getData() as $index => $items) {
            $time = Schedule::astr2academ(Yii::$app->formatter->asTimestamp($items->datetime_out) - Yii::$app->formatter->asTimestamp($items->datetime_in));
            $dataSchedule[] = [
                'rank' => 'item',
                'index' => $index,
                'time' => $this->getTime($items),
                'time_load' => $time,
                'sect_name' => $items->sect_name,
                'subject_type' => RefBook::find('subject_type_name_dev')->getValue($items->subject_type_id),
                'subject' => $items->subject,
                'direction' => $direction_list[$items->direction_id] ?? '',
                'auditory' => $auditory_list[$items->auditory_id] ?? '',
            ];

        }
//        echo '<pre>' . print_r($dataSchedule, true) . '</pre>'; die();
        $output_file_name = str_replace('.', '_' . ArtHelper::slug(RefBook::find('teachers_fio')->getValue($this->teachers_id)) . '.' . Yii::$app->formatter->asDate(time(), 'php:Y_m_d H_i') . '.', basename(self::template_timesheet));

        $tbs = DocTemplate::get(self::template_timesheet)->setHandler(function ($tbs) use ($data, $dataSchedule) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('item', $dataSchedule);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

    protected function getTime($items)
    {
        $array = explode(' ', $items->datetime_out);
        return $items->datetime_in . ' - ' . $array[1];
    }

}

