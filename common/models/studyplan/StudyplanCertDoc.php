<?php

namespace common\models\studyplan;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use common\models\user\UserCommon;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use function morphos\Russian\inflectName;

class StudyplanCertDoc
{
    const template_cert = 'document/certificate_student.docx';

    protected $model;
    protected $student_id;
    protected $programm_id;
    protected $template;

    public static function getData($model)
    {
        return new self($model);
    }

    public function __construct($model)
    {
        $this->model = $model;
        $this->student_id = $model->student_id;
        $this->programm_id = $model->programm_id;
        $this->template = self::template_cert;
//        echo '<pre>' . print_r($this->getAttestationItemsProgress(), true) . '</pre>';
//        echo '<pre>' . print_r($this->getProtocolItemsProgress(), true) . '</pre>';
//        die();
    }

    /**
     * REGEXP_REPLACE : Удалляем + и -
     * @return array
     */
    protected function getAttestationItemsProgress()
    {
        $models = (new Query())->from('attestation_items_view a')
            ->select([
                'CONCAT(REGEXP_REPLACE(mark_label, \'\D\', \'\', \'g\'), \' (\',mark_hint, \')\') as mark',
                'CASE
                  WHEN (subject_cat_id = 1000) THEN \'Специальность\'
                  ELSE subject_name
                END as subject_name',
                'plan_year',
                'subject_vid_id',
                'CASE
                    WHEN (SELECT subject_part FROM education_programm_level_subject s
                    JOIN education_programm_level l
                    ON l.id = s.programm_level_id 
                    WHERE l.programm_id = a.programm_id 
                    AND l.course = a.course
                    AND a.subject_id = s.subject_id
                    AND a.subject_vid_id = s.subject_vid_id
                    AND a.subject_cat_id = s.subject_cat_id LIMIT 1
                    ) = 1 THEN \'О\'
                          ELSE \'В\'
                        END
                     as subject_part'
            ])
            ->where(['student_id' => $this->student_id])
            ->andWhere(['programm_id' => $this->programm_id])
            ->andWhere(['med_cert' => true])
            ->orderBy('plan_year, subject_cat_id')
            ->all();

        $models_protocol = (new Query())->from('schoolplan_protocol_items_view a') // ПА из протокола заменит ПА без протокола $models
            ->select([
                'CONCAT(REGEXP_REPLACE(mark_label, \'\D\', \'\', \'g\'), \' (\',mark_hint, \')\') as mark',
                'CASE
                  WHEN (subject_cat_id = 1000) THEN \'Специальность\'
                  ELSE subject_name
                END as subject_name',
                'plan_year',
                'subject_vid_id',
                'CASE
                    WHEN (SELECT subject_part FROM education_programm_level_subject s
                    JOIN education_programm_level l
                    ON l.id = s.programm_level_id 
                    WHERE l.programm_id = a.programm_id 
                    AND l.course = a.course
                    AND a.subject_id = s.subject_id
                    AND a.subject_vid_id = s.subject_vid_id
                    AND a.subject_cat_id = s.subject_cat_id LIMIT 1
                    ) = 1 THEN \'О\'
                          ELSE \'В\'
                        END
                     as subject_part'
            ])
            ->where(['student_id' => $this->student_id])
            ->andWhere(['programm_id' => $this->programm_id])
            ->andWhere(['med_cert' => true])
            ->orderBy('plan_year, subject_cat_id')
            ->all();

        $models_2 = (new Query())->from('attestation_items_view a')// ИА без протокола заменит ПА из протокола
            ->select([
                'CONCAT(REGEXP_REPLACE(mark_label, \'\D\', \'\', \'g\'), \' (\',mark_hint, \')\') as mark',
                'CASE
                  WHEN (subject_cat_id = 1000) THEN \'Специальность\'
                  ELSE subject_name
                END as subject_name',
                'plan_year',
                'subject_vid_id',
                'CASE
                    WHEN (SELECT subject_part FROM education_programm_level_subject s
                    JOIN education_programm_level l
                    ON l.id = s.programm_level_id 
                    WHERE l.programm_id = a.programm_id 
                    AND l.course = a.course
                    AND a.subject_id = s.subject_id
                    AND a.subject_vid_id = s.subject_vid_id
                    AND a.subject_cat_id = s.subject_cat_id LIMIT 1
                    ) = 1 THEN \'О\'
                          ELSE \'В\'
                        END
                     as subject_part'
            ])
            ->where(['student_id' => $this->student_id])
            ->andWhere(['programm_id' => $this->programm_id])
            ->andWhere(['fin_cert' => true])
            ->orderBy('plan_year, subject_cat_id')
            ->all();

        $models_protocol_2 = (new Query())->from('schoolplan_protocol_items_view a') // ИА из протокола заменит ИА без протокола
        ->select([
            'CONCAT(REGEXP_REPLACE(mark_label, \'\D\', \'\', \'g\'), \' (\',mark_hint, \')\') as mark',
            'CASE
                  WHEN (subject_cat_id = 1000) THEN \'Специальность\'
                  ELSE subject_name
                END as subject_name',
            'plan_year',
            'subject_vid_id',
            'CASE
                    WHEN (SELECT subject_part FROM education_programm_level_subject s
                    JOIN education_programm_level l
                    ON l.id = s.programm_level_id 
                    WHERE l.programm_id = a.programm_id 
                    AND l.course = a.course
                    AND a.subject_id = s.subject_id
                    AND a.subject_vid_id = s.subject_vid_id
                    AND a.subject_cat_id = s.subject_cat_id LIMIT 1
                    ) = 1 THEN \'О\'
                          ELSE \'В\'
                        END
                     as subject_part'
        ])
            ->where(['student_id' => $this->student_id])
            ->andWhere(['programm_id' => $this->programm_id])
            ->andWhere(['fin_cert' => true])
            ->orderBy('plan_year, subject_cat_id')
            ->all();
//        echo '<pre>' . print_r([$models, $models_protocol, $models_2, $models_protocol_2], true) . '</pre>'; die();
        $models = ArrayHelper::map(array_merge($models, $models_protocol, $models_2, $models_protocol_2), 'subject_name', 'mark', 'subject_part');  // попадают в матрицу последние проставленные оценки
        return $models;
    }

    /**
     * REGEXP_REPLACE : Удаляем + и -
     * @return array
     */
    protected function getProtocolItemsProgress()
    {
        $models = (new Query())->from('schoolplan_protocol_items_view')
            ->select([
                'CONCAT(REGEXP_REPLACE(mark_label, \'\D\', \'\', \'g\'), \' (\',mark_hint, \')\') as mark',
                'CASE
                  WHEN (subject_cat_id = 1000) THEN \'Специальность\'
                  ELSE subject_name
                END as subject_name',
                'plan_year',
                'subject_vid_id'
            ])
            ->where(['student_id' => $this->student_id])
            ->andWhere(['programm_id' => $this->programm_id])
            ->andWhere(['fin_cert' => true])
            ->orderBy('plan_year, subject_cat_id')
            ->all();

        $models = ArrayHelper::map($models, 'subject_name', 'mark');  // попадают в матрицу последние проставленные оценки
        return $models;
    }

    protected function filterProgrammName($text)
    {
        $text = preg_replace('/ПП$/', '', $text);
        $text = preg_replace('/ОП$/', '', $text);
        $text = preg_replace('/\d$/', '', $text);
        return trim($text);
    }

    /**
     * @param $id
     * @return array
     */
    protected function getEducationName($id)
    {
        $name = [];
        switch ($id) {
            case  1000:
                $name[0] = 'дополнительной предпрофессиональной программы';
                $name[1] = 'дополнительную предпрофессиональную программу в области музыкального искусства';
                break;
            case  1001:
                $name[0] = 'дополнительной общеразвивающей программы';
                $name[1] = 'дополнительную общеразвивающую программу в области музыкального искусства';
                break;
            case  1002:
                $name[0] = 'дополнительной предпрофессиональной программы';
                $name[1] = 'дополнительную предпрофессиональную программу в области изобразительного искусства';
                break;
            case  1003:
                $name[0] = 'дополнительной общеразвивающей программы';
                $name[1] = 'дополнительную общеразвивающую программу в области изобразительного искусства';
                break;
            case  1004:
                $name[0] = 'дополнительной общеразвивающей программы';
                $name[1] = 'дополнительную общеразвивающую программу в области хореографического искусства';
                break;
            case  1005:
                $name[0] = 'дополнительной общеразвивающей программы';
                $name[1] = 'дополнительную общеразвивающую программу в области театрального искусства';
                break;
            case  1006:
                $name[0] = 'дополнительной общеразвивающей программы';
                $name[1] = 'дополнительную общеразвивающую программу';
                break;
            case  1007:
                $name[0] = 'дополнительной общеразвивающей программы';
                $name[1] = 'дополнительную общеразвивающей программу в области декоративно-прикладного искусства';
                break;
            case  1008:
                $name[0] = 'образовательного курса в области развития';
                $name[1] = 'образовательный курс в области развития';
                break;
            default  :
                $name[0] = '';
                $name[1] = '';
                break;

        }
        return $name;
    }

    public function makeDocx()
    {
        $model = $this->model;
        $save_as = str_replace(' ', '_', $model->student->fullName);
        $studentFio = explode(' ', $model->student->fullName); // Полное имя ученика именительный
        //$studentFio = explode(' ', inflectName($model->student->fullName, 'дательный')); // Полное имя ученика дательный
        $spec = $model->getSpeciality();
        $term_mastering = $model->programm->term_mastering;

        $protocol = $attest_o = $attest_v = [];
        $models_protocol = $this->getProtocolItemsProgress();
        $marks = [];
        foreach ($models_protocol as $subject_name => $mark) {
            $marks[] = preg_replace('/[^0-9\.,]/', '', $mark);
            $protocol[] = [
                'rank' => 'protocol',
                'subject' => $subject_name,
                'mark' => trim(preg_replace('/[0-9\.,()]/', '', $mark)), // Убираем из протокола числа оценок
            ];
        }
        $models = $this->getAttestationItemsProgress();
        foreach ($models['О'] ?? [] as $subject_name => $mark) {
            $marks[] = preg_replace('/[^0-9\.,]/', '', $mark);
            $attest_o[] = [
                'rank' => 'attest_o',
                'subject' => $subject_name,
                'mark' => $mark,
            ];
        }
        foreach ($models['В'] ?? [] as $subject_name => $mark) {
            $marks[] = preg_replace('/[^0-9\.,]/', '', $mark);
            $attest_v[] = [
                'rank' => 'attest_v',
                'subject' => $subject_name,
                'mark' => $mark,
            ];
        }
        $average = count($marks) != 0 ? array_sum($marks) / count($marks) : 0;

        $data[] = [
            'rank' => 'doc',
            'text_top' => $model->student->userGender == UserCommon::GENDER_FEMALE ? 'освоила' : (UserCommon::GENDER_MALE ? 'освоил' : 'освоил(освоила)'),
            'text' => $model->student->userGender == UserCommon::GENDER_FEMALE ? 'прошла'  : (UserCommon::GENDER_MALE ? 'прошел' : 'прошел(прошла)'),
            'student_first_name' => $studentFio[0],
            'student_last_name' => $studentFio[1] . ' ' . $studentFio[2],
            'speciality' => $spec ? '- ' . $spec : '',
            'is_honors' => $average == 5  ? 'с отличием' : '',
            'programm_name' => $this->filterProgrammName($model->programm->name), // название программы
            'programm_cat_top' => $this->getEducationName($model->programm->educationCat->id)[0], // название категории программы
            'programm_cat' => $this->getEducationName($model->programm->educationCat->id)[1], // название категории программы
            'term_mastering_pur' => $term_mastering . ' ' . ArtHelper::per($term_mastering), // Срок обучения
        ];

        $output_file_name = str_replace('.', '_' . $save_as . '_' . $model->doc_date . '.', basename($this->template));
        $tbs = DocTemplate::get($this->template)->setHandler(function ($tbs) use ($data, $protocol, $attest_v, $attest_o) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('protocol', $protocol);
            $tbs->MergeBlock('attest_v', $attest_v);
            $tbs->MergeBlock('attest_o', $attest_o);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

}

