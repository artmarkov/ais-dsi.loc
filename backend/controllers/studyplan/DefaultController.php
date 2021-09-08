<?php

namespace backend\controllers\studyplan;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\DocTemplate;
use artsoft\helpers\PriceHelper;
use backend\models\Model;
use common\models\education\EducationProgramm;
use common\models\education\EducationProgrammLevel;
use common\models\history\StudyplanHistory;
use common\models\parents\Parents;
use common\models\students\Student;
use common\models\studyplan\StudyplanSubject;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\studyplan\Studyplan model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\studyplan\Studyplan';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;
        $model->student_id = Yii::$app->request->get('student_id') ?: null;

        if ($model->load(Yii::$app->request->post())) {
            // validate all models
            $valid = $model->validate();
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $modelProgrammLevel = EducationProgrammLevel::find()
                        ->where(['programm_id' => $model->programm_id])
                        ->andWhere(['course' => $model->course])
                        ->one();
                    if ($modelProgrammLevel) {
                        $model->year_time_total = $modelProgrammLevel->year_time_total;
                        $model->cost_month_total = $modelProgrammLevel->cost_month_total;
                        $model->cost_year_total = $modelProgrammLevel->cost_year_total;
                    }
                    if ($flag = $model->save(false)) {

                        if (isset($modelProgrammLevel->educationProgrammLevelSubject)) {
                            $modelsSubTime = $modelProgrammLevel->educationProgrammLevelSubject;
                            foreach ($modelsSubTime as $modelSubTime) {
                                $modelSub = new StudyplanSubject();
                                $modelSub->studyplan_id = $model->id;
                                $modelSub->subject_cat_id = $modelSubTime->subject_cat_id;
                                $modelSub->subject_id = $modelSubTime->subject_id;
                                $modelSub->subject_type_id = $model->getTypeScalar();
                                $modelSub->subject_vid_id = $modelSubTime->subject_vid_id;
                                $modelSub->week_time = $modelSubTime->week_time;
                                $modelSub->year_time = $modelSubTime->year_time;
                                $modelSub->cost_hour = $modelSubTime->cost_hour;
                                $modelSub->cost_month_summ = $modelSubTime->cost_month_summ;
                                $modelSub->cost_year_summ = $modelSubTime->cost_year_summ;
                                $modelSub->year_time_consult = $modelSubTime->year_time_consult;

                                if (!($flag = $modelSub->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'modelsDependence' => [new StudyplanSubject],
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws \yii\db\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanSubject was not found.");
        }

        $modelsDependence = $model->studyplanSubject;

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsDependence, 'id', 'id');
            $modelsDependence = Model::createMultiple(StudyplanSubject::class, $modelsDependence);
            Model::loadMultiple($modelsDependence, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDependence, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsDependence) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            StudyplanSubject::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsDependence as $modelDependence) {
                            $modelDependence->studyplan_id = $model->id;
                            if (!($flag = $modelDependence->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        if (Yii::$app->request->post('submitAction') == 'doc_contract') {
//            echo '<pre>' . print_r($model->programm, true) . '</pre>';
           // echo '<pre>' . print_r($model->student, true) . '</pre>';
//            echo '<pre>' . print_r($model->parent, true) . '</pre>';
//            echo '<pre>' . print_r($modelsDependence, true) . '</pre>';
            $modelProgrammLevel = EducationProgrammLevel::find()
                ->where(['programm_id' => $model->programm_id])
                ->andWhere(['course' => $model->course])
                ->one();
            // echo '<pre>' . print_r($modelProgrammLevel->level->name, true) . '</pre>';
            $template = 'document/contract_student.docx';
            $save_as = str_replace(' ', '_', $model->student->fullName);
            $data = array();

            $data[] = [
                'rank' => 'doc',
                'doc_date' => date('j', strtotime($model->doc_date)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_date))] . ' ' . date('Y', strtotime($model->doc_date)), // дата договора
                'doc_signer' => $model->parent->fullName, // Полное имя подписанта-родителя
                'doc_student' => $model->student->fullName, // Полное имя ученика
                'doc_contract_start' => date('j', strtotime($model->doc_contract_start)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_contract_start))] . ' ' . date('Y', strtotime($model->doc_contract_start)), // дата начала договора
                'doc_contract_end' => date('j', strtotime($model->doc_contract_end)) . ' ' . ArtHelper::getMonthsList()[date('n', strtotime($model->doc_contract_end))] . ' ' . date('Y', strtotime($model->doc_contract_end)),$model->doc_contract_end, // Дата окончания договора
                'programm_name' => $model->programm->name, // название программы
                'programm_level' => $modelProgrammLevel->level->name, // уровень программы
                'term_mastering' => $model->programm->term_mastering, // Срок освоения образовательной программы
                'cost_year_total' => $model->cost_year_total, // Полная стоимость обучения
                'cost_year_total_str' => PriceHelper::num2str($model->cost_year_total), // Полная стоимость обучения прописью
                'student_address' => $model->student->userAddress,
                'student_phone' => $model->student->userPhone,
                'student_sert_name' => Student::getDocumentValue($model->student->sert_name),
                'student_sert_series' => $model->student->sert_series,
                'student_sert_num' => $model->student->sert_num,
                'student_sert_organ' => $model->student->sert_organ,
                'student_sert_date' => $model->student->sert_date,
                'parent_address' => $model->parent->userAddress,
                'parent_phone' => $model->parent->userPhone,
                'parent_sert_name' => Parents::getDocumentValue($model->parent->sert_name),
                'parent_sert_series' => $model->parent->sert_series,
                'parent_sert_num' => $model->parent->sert_num,
                'parent_sert_organ' => $model->parent->sert_organ,
                'parent_sert_date' => $model->parent->sert_date,



            ];
//            echo '<pre>' . print_r($data, true) . '</pre>';
            $output_file_name = str_replace('.', '_' . $save_as . '_' . $model->doc_date . '.', basename($template));

            $tbs = DocTemplate::get($template)->setHandler(function ($tbs) use ($data) {
                /* @var $tbs clsTinyButStrong */
                $tbs->MergeBlock('doc', $data);

            })->prepare();
            $tbs->Show(OPENTBS_DOWNLOAD, $output_file_name);
            exit;
        }
        return $this->render('update', [
            'model' => $model,
            'modelsDependence' => (empty($modelsDependence)) ? [new StudyplanSubject] : $modelsDependence,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public
    function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new StudyplanHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }

    /**
     *  формируем список дисциплин для widget DepDrop::classname()
     * @return false|string
     */
    public
    function actionSpeciality()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = EducationProgramm::getSpecialityByProgrammId($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    /**
     * формируем список дисциплин для widget DepDrop::classname()
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public
    function actionSubject($id)
    {
        $model = $this->findModel($id);
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = $model->getSubjectById($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }
}