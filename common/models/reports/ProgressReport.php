<?php

namespace common\models\reports;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use common\models\education\LessonProgressView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;


class ProgressReport
{
    protected $teachers_id;
    protected $teachers_fio;
    protected $plan_year;
    protected $plan_year_next;
    protected $date_in;
    protected $date_out;
    protected $sect_list;
    protected $subject_key;
    protected $model_date;
    protected $history;

    public function __construct($model_date)
    {
        $this->model_date = $model_date;
        $this->teachers_id = $model_date->teachers_id;
        $this->teachers_fio = RefBook::find('teachers_fullname')->getValue($this->teachers_id);
        $this->plan_year = $model_date->plan_year;
        $this->plan_year_next = $model_date->plan_year + 1;
        $this->history = $model_date->is_history;
        $timestamp = ArtHelper::getStudyYearParams($model_date->plan_year);
        $this->date_in = Yii::$app->formatter->asDate($timestamp['timestamp_in'], 'php:m.Y');
        $this->date_out = Yii::$app->formatter->asDate($timestamp['timestamp_out'], 'php:m.Y');
        $this->sect_list = LessonProgressView::getSectListForTeachersQuery($this->teachers_id, $this->plan_year, $this->history)->column();
        $this->subject_key = LessonProgressView::getIndivListForTeachers($this->teachers_id, $this->plan_year);

        // echo '<pre>' . print_r($this->plan_year + 1, true) . '</pre>';
        // $this->template_name = self::template_studyplan_history;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    protected function getData($subject_sect_studyplan_id)
    {
        $this->model_date->addRule(['date_in', 'date_out', 'subject_sect_studyplan_id'], 'safe');
        $this->model_date->date_in = $this->date_in;
        $this->model_date->date_out = $this->date_out;
        $this->model_date->subject_sect_studyplan_id = $subject_sect_studyplan_id;
        $model = LessonProgressView::getDataTeachers($this->model_date, $this->teachers_id, $this->plan_year, false, $this->history);
        // echo '<pre>' . print_r($model, true) . '</pre>'; die();
        return $model;
    }

    protected function getDataIndiv($subject_key)
    {
        $this->model_date->addRule(['date_in', 'date_out', 'subject_key'], 'safe');
        $this->model_date->date_in = $this->date_in;
        $this->model_date->date_out = $this->date_out;
        $this->model_date->subject_key = $subject_key;
        $model = LessonProgressView::getDataIndivTeachers($this->model_date, $this->teachers_id, $this->plan_year, false, $this->history);
        // echo '<pre>' . print_r($model, true) . '</pre>'; die();
        return $model;
    }

    /**
     *
     * @throws \yii\db\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */

    public function sendXls()
    {
        ini_set('memory_limit', '2024M');

        $cc = range('A', 'Z');
        $c = range('A', 'Z');
        foreach ($c as $ic) $cc[] = 'A' . $ic;
        foreach ($c as $ic) $cc[] = 'B' . $ic;
        foreach ($c as $ic) $cc[] = 'C' . $ic;
        foreach ($c as $ic) $cc[] = 'D' . $ic;
        foreach ($c as $ic) $cc[] = 'E' . $ic;
        foreach ($c as $ic) $cc[] = 'F' . $ic;

        $spr = new Spreadsheet();
        $i = 0;
        // групповые
        foreach ($this->sect_list as $item => $subject_sect_studyplan_id) {
            $dataArray = $this->getData($subject_sect_studyplan_id);
            $spr = $this->getSheetData($spr, $cc, $dataArray, $i);
            $i++;
        }
        // индивидуальные
        foreach (array_keys($this->subject_key) as $item => $subject_key) {
            $dataArray = $this->getDataIndiv($subject_key);
            $spr = $this->getSheetData($spr, $cc, $dataArray, $i);
            $i++;
        }
        $tmplName = ArtHelper::slug($this->teachers_fio) . '-' . (string)$this->plan_year . '.xlsx';
        $patch = Yii::getAlias('@runtime/');
        $template = $patch . $tmplName;
        $writer = new Xlsx($spr);
        $writer->save($template);

        if (file_exists($template)) {
            Yii::$app->response->sendFile($template, $tmplName)->send();
            unlink($template);
        }
    }

    /**
     * @param $spr
     * @param $dataArray
     * @param $i
     * @return mixed
     */
    protected function getSheetData($spr, $cc, $dataArray, $i)
    {

        // echo '<pre>' . print_r(count($cc), true) . '</pre>'; die();
        $spr->createSheet();
        $spr->setActiveSheetIndex($i);

        $field_value = $dataArray['attributes'];
        unset($field_value['studyplan_subject_id']);
        unset($field_value['subject_sect_studyplan_id']);
        $data = $dataArray['data'];
        $columns = $dataArray['columns'];

        // echo '<pre>' . print_r(array_keys($columns), true) . '</pre>'; die();
        $old = 0;
        foreach (array_keys($columns) as $k => $n) { // первая строка
            $spr->getActiveSheet()->setCellValue($cc[($k + 1 + $old)] . '6', $n)->mergeCells($cc[($k + 1 + $old)] . '6:' . $cc[($k + $columns[$n] + $old)] . '6');
            $old = $columns[$n] + $old - 1;
        }
        foreach (array_keys($field_value) as $k => $n) { // вторая строка
            $spr->getActiveSheet()->setCellValue($cc[($k)] . '7', $field_value[$n]);
        }
        foreach ($data as $s => $coll) {
            $spr->getActiveSheet()->setTitle(mb_substr($coll['subject'], 0, 30, 'UTF-8'));
            //$spr->getActiveSheet()->setCellValue($cc[0] . ($s + 1), (string)$s);
            foreach (array_keys($field_value) as $k => $n) {

                switch ($n) {
                    case 'student_id' :
                        $coll[$n] = $coll['student_fio'];
                        $spr->getActiveSheet()->getColumnDimension($cc[$k])->setWidth(50);

                        break;
                }
                $spr->getActiveSheet()->setCellValue($cc[$k] . ($s + 8), $coll[$n] ?? '');
                // $spr->getActiveSheet()->getColumnDimension($cc[$k])/*->setAutoSize(true)*/;
            }
        }
        $planYearStr = $this->plan_year . '/' . $this->plan_year_next;
        $spr->getActiveSheet()->setCellValue($cc[0] . 1, 'Выписка из журнала за : ' . $planYearStr . ' учебный год');
        $spr->getActiveSheet()->setCellValue($cc[0] . 3, 'Преподаватель : ' . $this->teachers_fio);
        $spr->getActiveSheet()->setCellValue($cc[0] . 4, 'Предмет : ' . $coll['subject']);
        $spr->getActiveSheet()->setCellValue($cc[0] . 5, 'Группа : ' . $coll['sect_name']);
        $spr->getActiveSheet()->setCellValue($cc[0] . ($s + 10), 'Сокращения Вид занятия:ПА - Промежуточная аттестация(оценка); ИА - Итоговая аттестация(оценка); ТР - Текущая работа; ИП - Итоговый просмотр (для выпускников); КУ - Контрольный урок/Зачет; ПП - Промежуточный просмотр; ДЗ - Домашнее задание; ЛР - Летняя работа; ЭП - Экзаменационный просмотр; Реф - Реферат; Экз. - Экзамен; Экз.ус. - Экзамен устно(сольф.); Экз.пис. - Экзамен писменно(сольф.);');
        $spr->getActiveSheet()->setCellValue($cc[0] . ($s + 12), 'Сокращения Оценки: ЗЧ - Зачет; НЗ - Незачет; НА - Не аттестован; Н - Отсутствие по неуважительной причине; П - Отсутствие по уважительной причине; Б - Отсутствие по причине болезни; О - Опоздание на урок; * - Факт присутствия(без оценки); 2 - неудовлетворительно; 3- - удовлетворительно; 3 - удовлетворительно; 3+ - удовлетворительно; 4- - хорошо; 4 - хорошо; 4+ - хорошо; 5- - отлично; 5 - отлично; 5+ - отлично');
        $spr->getActiveSheet()->getStyle('A6:PZ7')->getFont()->setBold(true);
        $spr->getActiveSheet()->freezePane('A8');
        $spr->getActiveSheet()->freezePane('B8');

        return $spr;
    }
}
