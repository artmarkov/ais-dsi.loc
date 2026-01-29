<?php

namespace common\models\studyplan;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\RefBook;
use common\models\education\AttestationItems;
use common\models\education\LessonItemsProgressView;
use common\models\education\LessonProgressView;
use common\models\schoolplan\SchoolplanPerform;
use common\models\schoolplan\SchoolplanProtocol;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class StudyplanHistory
{
    const template_studyplan_history = 'document/studyplan_history.xlsx';

    protected $studyplan_id;
    protected $plan_year;
    protected $template_name;
    protected $studyplanThematic;
    protected $data;
    protected $teachers_list;

    public function __construct($studyplan_id)
    {
        $this->studyplan_id = $studyplan_id;
        $this->teachers_list = RefBook::find('teachers_fio')->getList();
        $this->template_name = self::template_studyplan_history;
        $this->data = $this->getdata();
        $this->studyplanThematic = $this->getStudyplanThematic();
//        echo '<pre>' . print_r($studyplanThematic, true) . '</pre>';
    }

    protected function getStudyplanThematic()
    {
        return StudyplanThematicView::find()
            ->where(['studyplan_id' => $this->studyplan_id])
            ->andWhere(['IS NOT', 'studyplan_thematic_id', NULL])
            ->orderBy('subject_sect_studyplan_id, studyplan_subject_id, half_year')
            ->all();
    }

    protected function getProgressData()
    {
        $models = LessonProgressView::find()->where(['studyplan_id' => $this->studyplan_id])->asArray()->all();
        $studyplanSubjectIds = ArrayHelper::getColumn($models, 'studyplan_subject_id');

        $modelsMarksCertif = ArrayHelper::index((new Query())->from('attestation_items_view')
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['studyplan_subject_id' => $studyplanSubjectIds])
            ->andWhere(['IS NOT', 'lesson_mark_id', NULL])
            ->all(), null, ['studyplan_subject_id']);

        $modelsMarksProtocolOld = ArrayHelper::index(LessonItemsProgressView::find()
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['studyplan_id' => $this->studyplan_id])
            ->andWhere(['=', 'test_category', 3])
            ->andWhere(['IS NOT', 'lesson_mark_id', NULL])
            ->asArray()
            ->all(), null, ['studyplan_subject_id']);

        $modelsMarksCertifOld = ArrayHelper::index(LessonItemsProgressView::find()
            ->where(['plan_year' => $this->plan_year])
            ->andWhere(['studyplan_id' => $this->studyplan_id])
            ->andWhere(['=', 'test_category', 2])
            ->andWhere(['IS NOT', 'lesson_mark_id', NULL])
            ->asArray()
            ->all(), null, ['studyplan_subject_id']);

        $modelsMarksProtocol = ArrayHelper::index((new Query())->from('schoolplan_protocol_items_view')
            ->where(['studyplan_subject_id' => $studyplanSubjectIds])
            ->andWhere(['plan_year' => $this->plan_year])
            ->andWhere(['fin_cert' => true])
            ->andWhere(['IS NOT', 'lesson_mark_id', NULL])
            ->all(), null, ['studyplan_subject_id']);
        
        $data['progressdoc'] = [];
        foreach ($models as $ids => $model) {
            $teachers = [];
            foreach (explode(',', $model['teachers_list']) as $teachers_id) {
                $teachers[] = isset($this->teachers_list[$teachers_id]) ? $this->teachers_list[$teachers_id] : null;
            }
            $data['progressdoc'][$ids] = [
                'subject' => $model['subject'],
                'sect' => $model['sect_name'],
                'teachers_list' => implode(',', $teachers),
            ];
            if (isset($modelsMarksProtocol[$model['studyplan_subject_id']])) {
                foreach ($modelsMarksProtocol[$model['studyplan_subject_id']] as $item => $m) {
                    $data['progressdoc'][$ids]['items'][] = [
                        'item' => $item + 1,
                        'lesson_date' => Yii::$app->formatter->asDate($m['lesson_date'], 'php:d.m.Y'),
                        'test_name' => 'Итоговая аттестация',
                        'mark_label' => $m['mark_label'],
                    ];
                }
            } elseif (isset($modelsMarksProtocolOld[$model['studyplan_subject_id']])) {
                foreach ($modelsMarksProtocolOld[$model['studyplan_subject_id']] as $item => $m) {
                    $data['progressdoc'][$ids]['items'][] = [
                        'item' => $item + 1,
                        'lesson_date' => Yii::$app->formatter->asDate($m['lesson_date'], 'php:d.m.Y'),
                        'test_name' => $m['test_name'],
                        'mark_label' => $m['mark_label'],
                    ];
                }
            }
            if (isset($modelsMarksCertif[$model['studyplan_subject_id']])) {
                foreach ($modelsMarksCertif[$model['studyplan_subject_id']] as $item => $m) {
                    $data['progressdoc'][$ids]['items'][] = [
                        'item' => $item + 1,
                        'lesson_date' => Yii::$app->formatter->asDate($m['lesson_date'], 'php:d.m.Y'),
                        'test_name' => 'Промежуточная аттестация',
                        'mark_label' => $m['mark_label'],
                    ];
                }
            } elseif (isset($modelsMarksCertifOld[$model['studyplan_subject_id']])) {
                foreach ($modelsMarksCertifOld[$model['studyplan_subject_id']] as $item => $m) {
                    $data['progressdoc'][$ids]['items'][] = [
                        'item' => $item + 1,
                        'lesson_date' => Yii::$app->formatter->asDate($m['lesson_date'], 'php:d.m.Y'),
                        'test_name' => $m['test_name'],
                        'mark_label' => $m['mark_label'],
                    ];
                }
            } else {
                $data['progressdoc'][$ids]['items'][] = [
                    'item' =>  1,
                    'lesson_date' => '',
                    'test_name' => '',
                    'mark_label' => '',
                ];
            }
        }
//        echo '<pre>' . print_r($data, true) . '</pre>';
        return $data;
    }

    protected function getPerformData()
    {
        $models = SchoolplanPerform::find()->where(['studyplan_id' => $this->studyplan_id])->all();
        $studyplan_subject_list = RefBook::find('subject_memo_4')->getList();
        $data['performdoc'] = [];

        foreach ($models as $ids => $model) {
            $thematic_items_list = !empty($model->thematic_items_list[0]) ? StudyplanThematicItems::find()->select('topic')->where(['id' => $model->thematic_items_list])->column() : [];
            $data['performdoc'][] = [
                'date' => isset($model->schoolplan) ? Yii::$app->formatter->asDatetime($model->schoolplan->datetime_in) : '',
                'title' => isset($model->schoolplan) ? $model->schoolplan->title : '',
                'thematic_items_list' => implode('; ', $thematic_items_list),
                'resume' => $model->resume,
                'subject' => isset($studyplan_subject_list[$model->studyplan_subject_id]) ? $studyplan_subject_list[$model->studyplan_subject_id] : null,
                'teachers_fio' => isset($this->teachers_list[$model->teachers_id]) ? $this->teachers_list[$model->teachers_id] : null,
                'mark' => $model->lessonMark ? $model->lessonMark->mark_label : '',
                'winner' => $model->getWinnerValue($model->winner_id),
            ];
        }

        $models = SchoolplanProtocol::find()->joinWith('studyplanSubject')->where(['studyplan_id' => $this->studyplan_id])->all();
        foreach ($models as $ids => $model) {
            $thematic_items_list = !empty($model->thematic_items_list[0]) ? StudyplanThematicItems::find()->select('topic')->where(['id' => $model->thematic_items_list])->column() : [];
            $data['performdoc'][] = [
                'date' => isset($model->schoolplan) ? Yii::$app->formatter->asDatetime($model->schoolplan->datetime_in) : '',
                'title' => isset($model->schoolplan) ? $model->schoolplan->title : '',
                'thematic_items_list' => implode('; ', $thematic_items_list),
                'resume' => $model->resume,
                'subject' => isset($studyplan_subject_list[$model->studyplan_subject_id]) ? $studyplan_subject_list[$model->studyplan_subject_id] : null,
                'teachers_fio' => isset($this->teachers_list[$model->teachers_id]) ? $this->teachers_list[$model->teachers_id] : null,
                'mark' => $model->lessonMark ? $model->lessonMark->mark_label : '',
                'winner' => '',
            ];
        }
//        echo '<pre>' . print_r($models, true) . '</pre>';
        return $data;
    }

    protected function getData()
    {
        $model = $this->studyplanThematic[0];
        $this->plan_year = $model['plan_year'];
        $data['doc'] = [];
        $data['doc'] = [
            'plan_year' => $model['plan_year'] . '/' . ($model['plan_year'] + 1),
            'student_fio' => RefBook::find('students_fio')->getValue($model['student_id']),
            'programm_name' => RefBook::find('education_programm_name')->getValue($model['programm_id']),
            'course' => $model['course'],
        ];
        return $data;
    }

    protected function getThematicData()
    {
        $models = $this->studyplanThematic;
        $studyplanThematicIds = ArrayHelper::getColumn($models, 'studyplan_thematic_id');
        $query = StudyplanThematicItems::find()->where(['studyplan_thematic_id' => $studyplanThematicIds])->asArray()->all();
        $modelItems = ArrayHelper::index($query, null, ['studyplan_thematic_id']);
//        echo '<pre>' . print_r($modelItems, true) . '</pre>';

        $data['subjectdoc'] = [];
        foreach ($models as $ids => $model) {
            $data['subjectdoc'][$ids] = [
                'half_year' => ArtHelper::getHalfYearValue($model['half_year']),
                'subject' => $model['subject'],
                'sect' => $model['sect_name'],
                'teachers_fio' => RefBook::find('teachers_fio')->getValue($model['teachers_id']),
            ];
            if (isset($modelItems[$model['studyplan_thematic_id']])) {
                foreach ($modelItems[$model['studyplan_thematic_id']] as $item => $m) {
                    $data['subjectdoc'][$ids]['items'][] = [
                        'item' => $item + 1,
                        'topic' => $m['topic'],
                        'task' => $m['task'],
                    ];
                }
            }
        }
        return $data;
    }

    public function makeXlsx()
    {
        $data = $this->getData();
        $thematicData = $this->getThematicData();
        $progressData = $this->getProgressData();
        $performData = $this->getPerformData();

        $output_file_name = Yii::$app->formatter->asDate(time(), 'php:Y-m-d_H-i-s') . '_' . basename($this->template_name);

        $tbs = DocTemplate::get($this->template_name)->setHandler(function ($tbs) use ($data, $thematicData, $progressData, $performData) {
            /* @var $tbs clsTinyButStrong */
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('subjectdoc', $thematicData['subjectdoc']);
            $tbs->PlugIn(OPENTBS_SELECT_SHEET, 2);
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('progressdoc', $progressData['progressdoc']);
            $tbs->PlugIn(OPENTBS_SELECT_SHEET, 3);
            $tbs->MergeBlock('doc', $data);
            $tbs->MergeBlock('performdoc', $performData['performdoc']);

        })->prepare();
        $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
        exit;
    }

}
