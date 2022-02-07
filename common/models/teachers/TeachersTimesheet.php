<?php

namespace common\models\teachers;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\PriceHelper;
use artsoft\helpers\RefBook;
use artsoft\helpers\Schedule;
use common\models\education\EducationProgrammLevel;
use common\models\parents\Parents;
use common\models\students\Student;
use common\models\subjectsect\SubjectScheduleTeachersView;
use Yii;
use function morphos\Russian\inflectName;

class TeachersTimesheet
{
    const template_timesheet = 'document/tabel_teachers.xlsx';

    protected $date_in;
    protected $date_out;
    protected $mon;
    protected $year;
    protected $plan_year;

    public function __construct($model_date)
    {
        $this->date_in = $model_date->date_in;
        $this->date_out = $model_date->date_out;
        $this->mon = date('n', strtotime($this->date_in));
        $this->year = date('Y', strtotime($this->date_in));
        $this->plan_year = Schedule::getPlanYear($this->mon, $this->year);
    }

    protected function getTeachersDayFullTime($day, $direction_id, $teachers_id)
    {
        $week_day = Schedule::getWeekDay($day,  $this->mon, $this->year); // номер дня недели
        $week_num = Schedule::getWeekNum($day,  $this->mon, $this->year);  // номер недели в месяце

        $funcSql = <<< SQL
             select (SUM(time_out) - SUM(time_in)) as full_time from subject_schedule_teachers_view
                where direction_id = {$direction_id} 
                and teachers_id = {$teachers_id} 
                and week_day = {$week_day}
                and plan_year = {$this->plan_year}
                and case when week_num is not null then week_num = {$week_num} else week_num is null end
            SQL;

        $full_time = Yii::$app->db->createCommand($funcSql)->queryScalar();
        return $full_time > 0 ? Schedule::astr2academ($full_time) : null;
    }

    /**
     * формирование документов: Табель учета пед часов
     *
     * @param $template
     * @throws \yii\base\InvalidConfigException
     */
    public function makeXlsx()
    {

        $direction_id = 1000;
        $teachers_id = 1000;
        $modelsDependence = ['1' => 1];
        $day_in = date('d', strtotime($this->date_in));
        $day_out = date('d', strtotime($this->date_out));

        for ($day = $day_in; $day >= $day_out; $day++) {

            $full_time = $this->getTeachersDayFullTime($day, $direction_id, $teachers_id);
        }

        $save_as = str_replace(' ', '_', '1');
        $data[] = [
            'rank' => 'doc',
            'period_in' => date('j', strtotime($this->date_in)),
            'period_out' => date('j', strtotime($this->date_out)),
            'period_month' => ArtHelper::getMonthsList()[$this->mon],
            'period_year' => date('Y', strtotime($this->date_in)),
            'org_briefname' => Yii::$app->settings->get('own.shortname'),
            'departments' => '',
            'leader_iof' => Yii::$app->settings->get('own.head'),
            'employee_post' => '',
            'employee_iof' => '',
            'doc_data_mark' => Yii::$app->formatter->asDate(time(), 'php:d.m.Y'),
            'data_doc' => Yii::$app->formatter->asDate(time(), 'php:d.m.Y'),
            'tabel_num' => '',
//            'doc_date' => date('j', strtotime($model->doc_date)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_date))] . ' ' . date('Y', strtotime($model->doc_date)), // дата договора
//            'doc_signer' => $model->parent->fullName, // Полное имя подписанта-родителя
//            'doc_signer_iof' => RefBook::find('parents_iof')->getValue($model->parent->id),
//            'doc_signer_gen' => inflectName($model->parent->fullName, 'родительный'), // Полное имя подписанта-родителя родительный
//            'doc_signer_dat' => inflectName($model->parent->fullName, 'дательный'), // Полное имя подписанта-родителя дательный
//            'doc_student' => $model->student->fullName, // Полное имя ученика
//            'doc_student_gen' => inflectName($model->student->fullName, 'родительный'), // Полное имя ученика родительный
//            'doc_student_acc' => inflectName($model->student->fullName, 'винительный'), // Полное имя ученика винительный
//            'student_birth_date' => $model->student->userBirthDate, // День рождения ученика
//            'student_relation' => mb_strtolower(RefBook::find('parents_dependence_relation_name', $model->student_id)->getValue($model->parent->id), 'UTF-8'),
//            'doc_contract_start' => date('j', strtotime($model->doc_contract_start)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_contract_start))] . ' ' . date('Y', strtotime($model->doc_contract_start)), // дата начала договора
//            'doc_contract_end' => date('j', strtotime($model->doc_contract_end)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_contract_end))] . ' ' . date('Y', strtotime($model->doc_contract_end)), $model->doc_contract_end, // Дата окончания договора
//            'programm_name' => $model->programm->name, // название программы
//            'programm_level' => isset($modelProgrammLevel->level) ? $modelProgrammLevel->level->name : null, // уровень программы
//            'term_mastering' => 'Срок обучения:' . $model->programm->term_mastering, // Срок освоения образовательной программы
//            'course' => $model->course . ' класс',
//            'year_time_total' => $model->year_time_total,
//            'cost_month_total' => $model->cost_month_total,
//            'cost_year_total' => $model->cost_year_total, // Полная стоимость обучения
//            'cost_year_total_str' => PriceHelper::num2str($model->cost_year_total), // Полная стоимость обучения прописью
//            'student_address' => $model->student->userAddress,
//            'student_phone' => $model->student->userPhone,
//            'student_sert_name' => Student::getDocumentValue($model->student->sert_name),
//            'student_sert_series' => $model->student->sert_series,
//            'student_sert_num' => $model->student->sert_num,
//            'student_sert_organ' => $model->student->sert_organ,
//            'student_sert_date' => $model->student->sert_date,
//            'parent_address' => $model->parent->userAddress,
//            'parent_phone' => $model->parent->userPhone,
//            'parent_sert_name' => Parents::getDocumentValue($model->parent->sert_name),
//            'parent_sert_series' => $model->parent->sert_series,
//            'parent_sert_num' => $model->parent->sert_num,
//            'parent_sert_organ' => $model->parent->sert_organ,
//            'parent_sert_date' => $model->parent->sert_date,

        ];
        $items = [];
        foreach ($modelsDependence as $item => $modelDep) {
            $items[] = [
                'rank' => 'dep',
                'item' => $item + 1,
//                'subject_cat_name' => $modelDep->subjectCat->name,
//                'subject_name' => '(' . $modelDep->subject->name . ')',
//                'subject_type_name' => $modelDep->subjectType->name,
//                'subject_vid_name' => $modelDep->subjectVid->name,
//                'week_time' => $modelDep->week_time,
//                'year_time' => $modelDep->year_time,
//                'cost_hour' => $modelDep->cost_hour,
//                'cost_month_summ' => $modelDep->cost_month_summ,
//                'cost_year_summ' => $modelDep->cost_year_summ,
//                'year_time_consult' => $modelDep->year_time_consult,
            ];
        }
        $output_file_name = str_replace('.', '_' . $save_as . '_' . '.', basename(self::template_timesheet));

        $tbs = DocTemplate::get(self::template_timesheet)->setHandler(function ($tbs) use ($data, $items) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('dep', $items);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }
}
