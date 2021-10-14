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
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = Yii::t('art', 'Create');

        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;

        if ($model->load(Yii::$app->request->post())) {
            // validate all models
            $valid = $model->validate();
            // $valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $modelProgrammLevel = EducationProgrammLevel::find()
                        ->where(['programm_id' => $model->programm_id])
                        ->andWhere(['course' => $model->course])
                        ->one();
                    if ($modelProgrammLevel) {
                        $model->copyAttributes($modelProgrammLevel);
                    }
                    if ($flag = $model->save(false)) {

                        if (isset($modelProgrammLevel->educationProgrammLevelSubject)) {
                            $modelsSubTime = $modelProgrammLevel->educationProgrammLevelSubject;
                            foreach ($modelsSubTime as $modelSubTime) {
                                $modelSub = new StudyplanSubject();
                                $modelSub->copyAttributes($model, $modelSubTime);

                                if (!($flag = $modelSub->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction();
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
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
        $this->view->params['tabMenu'] = $this->getMenu($id);

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
                        $this->getSubmitAction();
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
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    public function actionHistory($id)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];
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

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка индивидуального плана', 'url' => ['/studyplan/default/update', 'id' => $id]],
            ['label' => 'Расписание занятий', 'url' => ['/studyplan/default/studyplan-schedule', 'id' => $id]],
            ['label' => 'Расписание консультаций', 'url' => ['/studyplan/default/studyplan-consult', 'id' => $id]],
            ['label' => 'Характеристики по предметам', 'url' => ['/studyplan/default/studyplan-characteristic', 'id' => $id]],
            ['label' => 'Дневник успеваемости', 'url' => ['/studyplan/default/studyplan-progress', 'id' => $id]],
            ['label' => 'Оплата за обучение', 'url' => ['/studyplan/default/studyplan-invoices', 'id' => $id]],
        ];
    }
}