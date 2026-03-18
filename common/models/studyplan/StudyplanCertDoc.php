<?php

namespace common\models\studyplan;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
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
        $models = ArrayHelper::map($models, 'subject_name', 'mark', 'subject_part');  // попадают в матрицу последние проставленные оценки
        return $models;
    }

    /**
     * REGEXP_REPLACE : Удалляем + и -
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

    protected function getEducationName($id)
    {
        switch ($id) {
            case  1000:
                $name = 'дополнительной предпрофессиональной программы в области музыкального искусства';
                break;
            case  1001:
                $name = 'дополнительной общеразвивающей программы в области музыкального искусства';
                break;
            case  1002:
                $name = 'дополнительной предпрофессиональной программы в области изобразительного искусства';
                break;
            case  1003:
                $name = 'дополнительной общеразвивающей программы в области изобразительного искусства';
                break;
            case  1004:
                $name = 'дополнительной общеразвивающей программы в области хореографического искусства';
                break;
            case  1005:
                $name = 'дополнительной общеразвивающей программы в области театрального искусства';
                break;
            case  1006:
                $name = 'дополнительной общеразвивающей программы в области искусств';
                break;
            case  1007:
                $name = 'дополнительной общеразвивающей программы в области декоративно - прикладного искусства';
                break;
            case  1008:
                $name = 'образовательного курса в области развития';
                break;
            default  :
                $name = '';
                break;

        }
        return $name;
    }

    public function makeDocx()
    {
        $model = $this->model;
        $save_as = str_replace(' ', '_', $model->student->fullName);
        $studentFio = explode(' ', inflectName($model->student->fullName, 'дательный')); // Полное имя ученика дательный
        $spec = $model->getSpeciality();
        $term_mastering = $model->programm->term_mastering;

        $data[] = [
            'rank' => 'doc',
            'student_first_name' => $studentFio[0],
            'student_last_name' => $studentFio[1] . ' ' . $studentFio[2],
            'speciality' => $spec ? '- ' . $spec : '',
            'programm_name' => $this->filterProgrammName($model->programm->name), // название программы
            'programm_cat' => $this->getEducationName($model->programm->educationCat->id), // название категории программы
            'term_mastering_pur' => $term_mastering . ' ' . ArtHelper::per($term_mastering), // Срок обучения
        ];

        $protocol = $attest_o = $attest_v = [];
        $models = $this->getProtocolItemsProgress();
        foreach ($models as $subject_name => $mark) {
            $protocol[] = [
                'rank' => 'protocol',
                'subject' => $subject_name,
                'mark' => $mark,
            ];
        }
        $models = $this->getAttestationItemsProgress();
        foreach ($models['О'] ?? [] as $subject_name => $mark) {
            $attest_o[] = [
                'rank' => 'attest_o',
                'subject' => $subject_name,
                'mark' => $mark,
            ];
        }
        foreach ($models['В'] ?? [] as $subject_name => $mark) {
            $attest_v[] = [
                'rank' => 'attest_v',
                'subject' => $subject_name,
                'mark' => $mark,
            ];
        }

//        echo '<pre>' . print_r(), true) . '</pre>'; die();

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

