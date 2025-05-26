<?php

namespace common\models\teachers;

use artsoft\fileinput\models\FileManager;
use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\info\Document;
use common\models\schedule\ConsultScheduleConfirm;
use common\models\schedule\ConsultScheduleView;
use Yii;
use yii\db\Exception;

class TeachersConsult
{
    const template_timesheet = 'document/teachers_consult.xlsx';

    protected $models;
    protected $plan_year;
    protected $plan_year_next;
    protected $teachers_id;
    protected $teachers_fio;
    protected $modelConfirm;
    protected $tmplName;
    protected $template;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->plan_year_next = $model_date->plan_year + 1;
        $this->teachers_id = $model_date->teachers_id;
        $this->teachers_fio = RefBook::find('teachers_fullname')->getValue($this->teachers_id);
        $this->modelConfirm = $this->getConfirmData();
        $this->tmplName = ArtHelper::slug($this->teachers_fio) . '-' . Yii::$app->formatter->asDate(time(), 'php:YmdHis') . '.xlsx';
        $this->template = Yii::getAlias('@runtime/') . $this->tmplName;
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
    public function saveXls()
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
     //   $output_file_name = str_replace('.', '_' . ArtHelper::slug(RefBook::find('teachers_fio')->getValue($this->teachers_id)) . '.' . Yii::$app->formatter->asDate(time(), 'php:Y_m_d H_i') . '.', basename(self::template_timesheet));

        return DocTemplate::get(self::template_timesheet)->setHandler(function ($tbs) use ($data, $dataSchedule) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('item', $dataSchedule);

        })->save($this->template);
    }

    /**
     * @param $this->tmplName
     * @throws \yii\base\InvalidConfigException
     */
    public function makeDocument()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $title = 'Выписки из расписания консультаций за ' . $this->plan_year . '/' . $this->plan_year_next . ' учебный год';
            $modelTeachers = Teachers::findOne($this->teachers_id);
            $modelDoc = Document::find()
                    ->where(['user_common_id' => $modelTeachers->user_common_id])
                    ->andWhere(['title' => $title])
                    ->one() ?? new Document();
            $modelDoc->user_common_id = $modelTeachers->user_common_id;
            $modelDoc->doc_date = Yii::$app->formatter->asDate(time());
            $modelDoc->title = $title;
            $flag = $modelDoc->save(false);
            $file = new FileManager();
            $file->orig_name = $this->tmplName;
            $file->name = $this->tmplName;
            $file->size = filesize($this->template);
            $file->type = 'xlsx';
            $file->item_id = $modelDoc->id;
            $file->class = 'Document';
            $file->sort = 0;
            $filename_new = Yii::getAlias('@frontend/') . "web/uploads/fileinput/document/" . $file->name;
            copy($this->template, $filename_new);
            $flag = $flag && $file->save(false);

            if ($flag) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            print_r($e->errorInfo);
            $transaction->rollBack();
        }
    }

    public function cliarTemp() {
        if (file_exists($this->template)) {
            unlink($this->template);
        }
    }

    public function uploadFile() {
        if (file_exists($this->template)) {
            Yii::$app->response->sendFile($this->template, $this->tmplName)->send();
        }
    }
    protected function getTime($items)
    {
        $array = explode(' ', $items->datetime_out);
        return $items->datetime_in . ' - ' . $array[1];
    }

}

