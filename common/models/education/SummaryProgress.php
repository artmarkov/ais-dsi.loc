<?php

namespace common\models\education;

use artsoft\helpers\ExcelObjectList;
use common\models\schoolplan\SchoolplanProtocol;
use common\models\students\Student;
use common\models\studyplan\Studyplan;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class SummaryProgress
{
    protected $plan_year;
    protected $education_cat_id;
    protected $programm_id;
//    protected $vid_sert;
    protected $subject_type_id;
    protected $subject_form_id;
    protected $course;
    protected $lessonItemsProgress;
    protected $protocolItemsProgress;
    protected $studyplanSubjectIds;
    protected $studyplanIds;
    protected $studyplan;
    protected $studyplanSubject;
    protected $studyplanSubjectAttr;
    protected $studyplanSubjectMarkNeeds;

    public function __construct($model_date)
    {
        $this->plan_year = $model_date->plan_year;
        $this->education_cat_id = $model_date->education_cat_id;
        $this->programm_id = $model_date->programm_id;
//        $this->vid_sert = $model_date->vid_sert ?: null;
        $this->subject_type_id = $model_date->subject_type_id;
        $this->subject_form_id = $model_date->subject_form_id;
        $this->course = $model_date->course;
        $this->studyplan = $this->getStudyplan();
        $this->studyplanIds = $this->getStudyplanIds();
        $this->studyplanSubject = $this->getStudyplanSubject();
        $this->studyplanSubjectIds = $this->getStudyplanSubjectIds();
        $this->studyplanSubjectAttr = $this->getStudyplanSubjectAttr();
        $this->lessonItemsProgress = $this->getLessonItemsProgress();
        $this->protocolItemsProgress = $this->getProtocolItemsProgress();
        $this->studyplanSubjectMarkNeeds = $this->getStudyplanSubjectMarkNeeds();
      //  echo '<pre>' . print_r( $this->protocolItemsProgress, true) . '</pre>'; die();

    }

    protected function getStudyplan()
    {
        if (!$this->programm_id) {
            return [];
        }
        $models = (new \yii\db\Query())->from('studyplan_view')
            ->select('student_id')
            ->where(['programm_id' => $this->programm_id])
            ->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ])
            ->andWhere(['plan_year' => $this->plan_year]);

        if ($this->subject_form_id) {
            $models = $models->andWhere(['subject_form_id' => $this->subject_form_id]);
        }
        if ($this->course) {
            $models = $models->andWhere(['course' => $this->course]);
        }
        $models = $models->column();
        $studentIds = $models;

        $models = (new \yii\db\Query())->from('studyplan_view')
            ->where(['programm_id' => $this->programm_id])
            ->andWhere(['student_id' => $studentIds])
            ->andWhere(['<=', 'plan_year', $this->plan_year]);
        $models = $models->orderBy('student_fio ASC, course DESC')->all();
        return $models;
    }

    protected function getStudyplanIds()
    {
        $studyplan = ArrayHelper::index($this->studyplan, 'id');
        return array_keys($studyplan);
    }

    protected function getStudyplanSubject()
    {
        $models = (new \yii\db\Query())->from('studyplan_subject_view')
            ->where(['studyplan_id' => $this->studyplanIds]);
        if ($this->subject_type_id) {
            $models = $models->andWhere(['subject_type_id' => $this->subject_type_id]);
        }
//        if ($this->vid_sert == LessonTest::MIDDLE_ATTESTATION) {
//            $models = $models->andWhere(['med_cert' => true]);
//        }
//        if ($this->vid_sert == LessonTest::FINISH_ATTESTATION) {
//            $models = $models->andWhere(['fin_cert' => true]);
//        }
        $models = $models->andWhere(['OR', ['med_cert' => true], ['fin_cert' => true]]);
        $models = $models->orderBy('subject_category_id, subject_vid_id, subject_id')->all();

        return $models;
    }

    protected function getStudyplanSubjectIds()
    {
        $models = ArrayHelper::getColumn($this->studyplanSubject, 'studyplan_subject_id');
        return array_unique($models);
    }


    protected function getStudyplanSubjectAttr()
    {
        $models = (new \yii\db\Query())->from('studyplan_subject_view')
            ->select([
                'subject_category_id', 'subject_category_name', 'subject_category_slug',
                'subject_vid_id', 'subject_vid_name', 'subject_vid_slug',
                //'subject_type_id', 'subject_type_name', 'subject_type_slug',// бало задвоение предметов
                'subject_id', 'subject_name', 'subject_slug',
                'concat(subject_id, \'|\', subject_vid_id, \'|\', subject_category_id) as subject_key'
            ])
            ->distinct()
            ->where(['studyplan_subject_id' => $this->studyplanSubjectIds])
            ->orderBy('subject_category_id, subject_vid_id, subject_id')
            ->all();
        return $models;
    }

    protected function getLessonItemsProgress()
    {
        $models = LessonItemsProgressView::find()
            ->select([
                'studyplan_id', 'mark_label', 'studyplan_subject_id', 'subject_sect_studyplan_id', 'test_category', 'med_cert', 'fin_cert',
                'lesson_test_id', 'lesson_mark_id', 'test_name', 'lesson_date', 'lesson_progress_id',
                'concat(subject_id, \'|\', subject_vid_id, \'|\', subject_cat_id) as subject_key'
            ])
            ->where(['studyplan_subject_id' => $this->studyplanSubjectIds])
            // ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['OR',
                ['AND',
                    ['test_category' => LessonTest::MIDDLE_ATTESTATION],
                    ['med_cert' => true]],
                ['AND',
                    ['test_category' => LessonTest::FINISH_ATTESTATION],
                    ['fin_cert' => true]
                ]
            ])
//            ->asArray()
            ->all();
        return $models;
    }

    /**
     * @return array
     */
    protected function getProtocolItemsProgress()
    {
        $models = (new Query())->from('schoolplan_protocol_items_view')
            ->select([
                'studyplan_id', 'mark_label', 'studyplan_subject_id', 'med_cert', 'fin_cert',
                 'lesson_mark_id', 'plan_year',
                'concat(subject_id, \'|\', subject_vid_id, \'|\', subject_cat_id) as subject_key'
            ])
            ->where(['studyplan_subject_id' => $this->studyplanSubjectIds])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['fin_cert' => true])
            ->all();
        return $models;
    }

    protected function getStudyplanSubjectMarkNeeds()
    {
        $models = (new \yii\db\Query())->from('studyplan_subject_view')
            ->select([
                'studyplan_id',
                'concat(subject_id, \'|\', subject_vid_id, \'|\', subject_category_id) as subject_key'
            ])
            ->where(['studyplan_subject_id' => $this->studyplanSubjectIds])
            ->andWhere(['OR',
                ['status' => Studyplan::STATUS_ACTIVE],
                ['AND',
                    ['status' => Studyplan::STATUS_INACTIVE],
                    ['status_reason' => [1, 2, 4]]
                ]
            ]);
//        if ($this->vid_sert == LessonTest::MIDDLE_ATTESTATION) {
//            $models = $models->andWhere(['med_cert' => true]);
//        }
//        if ($this->vid_sert == LessonTest::FINISH_ATTESTATION) {
//            $models = $models->andWhere(['fin_cert' => true]);
//        }
        $models = $models->andWhere(['OR', ['med_cert' => true], ['fin_cert' => true]]);
        $models = $models->all();
        return ArrayHelper::index($models, 'subject_key', 'studyplan_id');
    }

    protected function getHeader()
    {
        $columnsHeader = $columnsSubHeader = [];
        $arr = ArrayHelper::index($this->studyplanSubjectAttr, null, ['subject_category_id']);
        $arr2 = ArrayHelper::index($this->studyplanSubjectAttr, null, ['subject_category_id', 'subject_vid_id']);
        foreach ($arr as $item => $subarr) {
            $columnsHeader[] = ['content' => $subarr[0]['subject_category_slug'], 'options' => ['colspan' => count($subarr), 'class' => 'text-center']];
            foreach ($arr2[$item] as $i => $sub) {
                $columnsSubHeader[] = ['content' => $sub[0]['subject_vid_slug'], 'options' => ['colspan' => count($sub), 'class' => 'text-center']];
            }
        }
        return [$columnsHeader, $columnsSubHeader];
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getData($readonly = false)
    {
        $studyplanProgress = ArrayHelper::index($this->lessonItemsProgress, 'subject_key', 'studyplan_id');
        $protocolProgress = ArrayHelper::index($this->protocolItemsProgress, 'subject_key', 'studyplan_id');
        $subjectKeys = ArrayHelper::getColumn($this->studyplanSubjectAttr, 'subject_key');
        $attributes = [
            'student_id' => 'ID',
            'student_fio' => 'ФИО ученика',
            'plan_year' => 'Учебный год',
            'education_cat_short_name' => 'Кат.',
            'education_programm_short_name' => 'Прогр.',
//            'course' => 'Класс',
            'subject_form_name' => 'Форма',
        ];
        $attributes += ArrayHelper::map($this->studyplanSubjectAttr, 'subject_key', 'subject_slug');
        $data = $dataNeeds = [];
//        echo '<pre>' . print_r($studyplanProgress, true) . '</pre>';
//        echo '<pre>' . print_r($studyplanProgress, true) . '</pre>';
//        die();
        $students = ArrayHelper::index($this->studyplan, 'id', ['student_id']);
        foreach ($students as $id => $studyplans) {
            foreach ($studyplans as $studyplan_id => $model) {
                $data[$id]['studyplan_id'] = isset($data[$id]['studyplan_id']) ? $data[$id]['studyplan_id'] : $model['id'];
                $data[$id]['student_id'] = $model['student_id'];
                $data[$id]['student_fio'] = $model['student_fio'];
                $data[$id]['plan_year'] = $model['plan_year'];
                $data[$id]['education_cat_short_name'] = $model['education_cat_short_name'];
                $data[$id]['education_programm_short_name'] = $model['education_programm_short_name'];
//            $data[$id]['course'] = $model['course'];
                $data[$id]['subject_form_name'] = $model['subject_form_name'];

                foreach ($subjectKeys as $item => $subject_key) {
//                    $data[$id][$subject_key] = null;
                    if(!isset($dataNeeds[$id][$subject_key]) ) {
                        $dataNeeds[$id][$subject_key] = isset($this->studyplanSubjectMarkNeeds[$studyplan_id][$subject_key]) ? true : false;
                    }
                    if(!isset($data[$id][$subject_key])) {
                        if (isset($protocolProgress[$studyplan_id][$subject_key])) {
                            $data[$id][$subject_key] = '<span style="font-size:85%; " class="label label-success">' . $protocolProgress[$studyplan_id][$subject_key]['mark_label'] . '</span>';
                        }
                        // если в протоколе нет оценки, то берем из журнала(потом отключить)
                        elseif (!isset($studyplanProgress[$studyplan_id][$subject_key]) && isset($this->studyplanSubjectMarkNeeds[$studyplan_id][$subject_key])) {  // только в случае если эта оценка недолжна быть в позднем плане
                            $data[$id][$subject_key] = '';
                        }elseif (isset($studyplanProgress[$studyplan_id][$subject_key])) {
                            $data[$id][$subject_key] = !$readonly ? LessonProgressView::getEditableForm($studyplanProgress[$studyplan_id][$subject_key]) : $studyplanProgress[$studyplan_id][$subject_key]['mark_label'];
                        }
                    }
                }
            }
        }
//        usort($data, function ($a, $b) {
//            return $b['total'] <=> $a['total'];
//        });
//        echo '<pre>' . print_r($data, true) . '</pre>'; die();

        return ['data' => $data, 'dataNeeds' => $dataNeeds, 'subjectKeys' => $subjectKeys, 'attributes' => $attributes, 'header' => $this->getHeader()];
    }

    /**
     * @param $data
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendXlsx($data)
    {
        ini_set('memory_limit', '512M');
        try {
            $x = new ExcelObjectList($data['attributes']);
            foreach ($data['data'] as $item) { // данные
                $x->addData($item);
            }
//            $x->addData(['stake' => 'Итого', 'total' => $data['all_summ']]);

            \Yii::$app->response
                ->sendContentAsFile($x, strtotime('now') . '_' . Yii::$app->getSecurity()->generateRandomString(6) . '_studyplan-stat.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
                ->send();
            exit;
        } catch (\PhpOffice\PhpSpreadsheet\Exception | \yii\web\RangeNotSatisfiableHttpException $e) {
            \Yii::error('Ошибка формирования xlsx: ' . $e->getMessage());
            \Yii::error($e);
            Yii::$app->session->setFlash('error', 'Ошибка формирования xlsx-выгрузки');
            return true;
        }
    }

}
