<?php

namespace backend\controllers\education;

use common\models\education\EducationCat;
use common\models\education\EducationProgramm;
use common\models\education\EducationProgrammLevel;
use common\models\education\EducationProgrammLevelSubject;
use backend\models\Model;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\education\EducationProgramm model.
 */
class DefaultController extends MainController
{
    /**
     * @var array
     */
    public $freeAccessActions = ['programm', 'subject'];

    public $modelClass = 'common\models\education\EducationProgramm';
    public $modelSearchClass = 'common\models\education\search\EducationProgrammSearch';
    public $modelHistoryClass = 'common\models\history\EducationProgrammHistory';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $modelsEducationProgrammLevel = [new EducationProgrammLevel];
        $modelsEducationProgrammLevelSubject = [[new EducationProgrammLevelSubject]];

        if ($model->load(Yii::$app->request->post())) {

            $modelsEducationProgrammLevel = Model::createMultiple(EducationProgrammLevel::class);
            Model::loadMultiple($modelsEducationProgrammLevel, Yii::$app->request->post());

            // validate person and houses models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsEducationProgrammLevel) && $valid;

            if (isset($_POST['EducationProgrammLevelSubject'][0][0])) {
                foreach ($_POST['EducationProgrammLevelSubject'] as $index => $times) {
                    foreach ($times as $indexTime => $time) {
                        $data['EducationProgrammLevelSubject'] = $time;
                        $modelEducationProgrammLevelSubject = new EducationProgrammLevelSubject;
                        $modelEducationProgrammLevelSubject->load($data);
                        $modelsEducationProgrammLevelSubject[$index][$indexTime] = $modelEducationProgrammLevelSubject;
                        $valid = $modelEducationProgrammLevelSubject->validate();
                    }
                }
            }
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsEducationProgrammLevel as $index => $modelEducationProgrammLevel) {

                            if ($flag === false) {
                                break;
                            }
                            $modelEducationProgrammLevel->programm_id = $model->id;

                            if (!($flag = $modelEducationProgrammLevel->save(false))) {
                                break;
                            }

                            if (isset($modelsEducationProgrammLevelSubject[$index]) && is_array($modelsEducationProgrammLevelSubject[$index])) {
                                foreach ($modelsEducationProgrammLevelSubject[$index] as $indexTime => $modelEducationProgrammLevelSubject) {
                                    $modelEducationProgrammLevelSubject->programm_subject_id = $modelEducationProgrammLevel->id;
                                    if (!($flag = $modelEducationProgrammLevelSubject->save(false))) {
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        $this->getSubmitAction($model);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax($this->createView, [
                'model' => $model,
                'modelsEducationProgrammLevel' => (empty($modelsEducationProgrammLevel)) ? [new EducationProgrammLevel] : $modelsEducationProgrammLevel,
                'modelsEducationProgrammLevelSubject' => (empty($modelsEducationProgrammLevelSubject)) ? [[new EducationProgrammLevelSubject]] : $modelsEducationProgrammLevelSubject,
                'readonly' => false
            ]
        );
    }

    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The EducationProgramm was not found.");
        }

        $modelsEducationProgrammLevel = $model->programmLevel;
        $modelsEducationProgrammLevelSubject = [];
        $oldTimes = [];

        if (!empty($modelsEducationProgrammLevel)) {
            foreach ($modelsEducationProgrammLevel as $index => $modelEducationProgrammLevel) {
                $times = $modelEducationProgrammLevel->educationProgrammLevelSubject;
                $modelsEducationProgrammLevelSubject[$index] = $times;
                $oldTimes = ArrayHelper::merge(ArrayHelper::index($times, 'id'), $oldTimes);
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            // reset
            $modelsEducationProgrammLevelSubject = [];

            $oldSubjectIDs = ArrayHelper::map($modelsEducationProgrammLevel, 'id', 'id');
            $modelsEducationProgrammLevel = Model::createMultiple(EducationProgrammLevel::class, $modelsEducationProgrammLevel);
            Model::loadMultiple($modelsEducationProgrammLevel, Yii::$app->request->post());
            $deletedSubjectIDs = array_diff($oldSubjectIDs, array_filter(ArrayHelper::map($modelsEducationProgrammLevel, 'id', 'id')));

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsEducationProgrammLevel) && $valid;

            $timesIDs = [];
            if (isset($_POST['EducationProgrammLevelSubject'][0][0])) {
                foreach ($_POST['EducationProgrammLevelSubject'] as $index => $times) {
                    $timesIDs = ArrayHelper::merge($timesIDs, array_filter(ArrayHelper::getColumn($times, 'id')));
                    foreach ($times as $indexTime => $time) {
                        $data['EducationProgrammLevelSubject'] = $time;
                        $modelEducationProgrammLevelSubject = (isset($time['id']) && isset($oldTimes[$time['id']])) ? $oldTimes[$time['id']] : new EducationProgrammLevelSubject;
                        $modelEducationProgrammLevelSubject->load($data);
                        $modelsEducationProgrammLevelSubject[$index][$indexTime] = $modelEducationProgrammLevelSubject;
                        $valid = $modelEducationProgrammLevelSubject->validate();
                    }
                }
            }

            $oldTimesIDs = ArrayHelper::getColumn($oldTimes, 'id');
            $deletedTimesIDs = array_diff($oldTimesIDs, $timesIDs);

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {

                        if (!empty($deletedTimesIDs)) {
                            EducationProgrammLevelSubject::deleteAll(['id' => $deletedTimesIDs]);
                        }

                        if (!empty($deletedSubjectIDs)) {
                            EducationProgrammLevel::deleteAll(['id' => $deletedSubjectIDs]);
                        }

                        foreach ($modelsEducationProgrammLevel as $index => $modelEducationProgrammLevel) {

                            if ($flag === false) {
                                break;
                            }
                            $modelEducationProgrammLevel->programm_id = $model->id;
                            if (!($flag = $modelEducationProgrammLevel->save(false))) {
                                break;
                            }

                            $modelEducationProgrammLevel = EducationProgrammLevel::findOne(['id' => $modelEducationProgrammLevel->id]);

                            if (isset($modelsEducationProgrammLevelSubject[$index]) && is_array($modelsEducationProgrammLevelSubject[$index])) {
                                foreach ($modelsEducationProgrammLevelSubject[$index] as $indexTime => $modelEducationProgrammLevelSubject) {
                                    $modelEducationProgrammLevelSubject->programm_level_id = $modelEducationProgrammLevel->id;

                                    if (!($flag = $modelEducationProgrammLevelSubject->save(false))) {
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        if (Yii::$app->request->post('submitAction') == 'copy') {
                            $model->copy();
                        }
                        return $this->getSubmitAction($model);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderIsAjax($this->updateView, [
            'model' => $model,
            'modelsEducationProgrammLevel' => (empty($modelsEducationProgrammLevel)) ? [new EducationProgrammLevel] : $modelsEducationProgrammLevel,
            'modelsEducationProgrammLevelSubject' => (empty($modelsEducationProgrammLevelSubject)) ? [[new EducationProgrammLevelSubject]] : $modelsEducationProgrammLevelSubject,
            'readonly' => $readonly
        ]);
    }

    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
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

    public function actionProgramm()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $cat_id = $parents[0];
                $out = EducationProgramm::getProgrammListById($cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

//    public function actionTest($id){
//        $model = $this->findModel($id);
//        $model->getSubjectListByProgramm();
//    }
//    public function actionSubjectVid($id)
//    {
//        $model = $this->findModel($id);
//        $out = [];
//        if (isset($_POST['depdrop_parents'])) {
//            $parents = $_POST['depdrop_parents'];
//
//            if (!empty($parents)) {
//                $cat_id = $parents[0];
//                $out = $model->getSubjectVidBySubjectId($cat_id);
//
//                return json_encode(['output' => $out, 'selected' => '']);
//            }
//        }
//        return json_encode(['output' => '', 'selected' => '']);
//    }
}