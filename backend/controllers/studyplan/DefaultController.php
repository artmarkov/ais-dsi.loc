<?php

namespace backend\controllers\studyplan;

use backend\models\Model;
use common\models\education\EducationProgramm;
use common\models\education\EducationProgrammLevel;
use common\models\history\StudyplanHistory;
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
            'readonly' => false,
            'indexAction' => null
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
            $model->makeDocx('document/contract_student.docx');
        } elseif (Yii::$app->request->post('submitAction') == 'doc_statement') {
            $model->makeDocx('document/statement_student.docx');
        }
        return $this->render('update', [
            'model' => $model,
            'modelsDependence' => (empty($modelsDependence)) ? [new StudyplanSubject] : $modelsDependence,
            'readonly' => $readonly,
            'indexAction' => null
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionHistory($id)
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
    public function actionSpeciality()
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
    public function actionSubject($id)
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