<?php

namespace common\models\studyplan;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\RefBook;
use common\models\teachers\Teachers;
use Yii;
use yii\db\Query;

class StudyplanDistrib
{
    const template = 'document/report_form_distrib.xlsx';

    protected $plan_year;
    protected $template_name;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->template_name = $this->template;
    }

    protected function getStudyplans()
    {
        $models = (new Query())->from('studyplan_stat_view')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['status' => Studyplan::STATUS_ACTIVE])
            ->all();
        return $models;
    }

    /**
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
