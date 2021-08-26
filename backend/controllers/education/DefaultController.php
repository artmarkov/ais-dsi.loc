<?php

namespace backend\controllers\education;

use common\models\education\EducationProgramm;
use common\models\education\EducationProgrammSubject;
use common\models\education\EducationProgrammSubjectTime;
use common\models\history\EducationProgrammHistory;
use backend\models\Model;
use common\models\subject\Subject;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\education\EducationProgramm model.
 */
class DefaultController extends MainController
{
    public $modelClass = 'common\models\education\EducationProgramm';
    public $modelSearchClass = 'common\models\education\search\EducationProgrammSearch';

    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        /* @var $model \artsoft\db\ActiveRecord */
        $model = new $this->modelClass;
        $modelsSubject = [new EducationProgrammSubject];
        $modelsTime = [[new EducationProgrammSubjectTime]];

        if ($model->load(Yii::$app->request->post())) {

            $modelsSubject = Model::createMultiple(EducationProgrammSubject::class);
            Model::loadMultiple($modelsSubject, Yii::$app->request->post());

            // validate person and houses models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsSubject) && $valid;

            if (isset($_POST['EducationProgrammSubjectTime'][0][0])) {
                foreach ($_POST['EducationProgrammSubjectTime'] as $index => $times) {
                    foreach ($times as $indexTime => $time) {
                        $data['EducationProgrammSubjectTime'] = $time;
                        $modelTime = new EducationProgrammSubjectTime;
                        $modelTime->load($data);
                        $modelsTime[$index][$indexTime] = $modelTime;
                        $valid = $modelTime->validate();
                    }
                }
            }

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsSubject as $index => $modelSubject) {

                            if ($flag === false) {
                                break;
                            }

                            $modelSubject->programm_id = $model->id;

                            if (!($flag = $modelSubject->save(false))) {
                                break;
                            }

                            if (isset($modelsTime[$index]) && is_array($modelsTime[$index])) {
                                foreach ($modelsTime[$index] as $indexTime => $modelTime) {
                                    $modelTime->programm_subject_id = $modelSubject->id;
                                    if (!($flag = $modelTime->save(false))) {
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
                'modelsSubject' => (empty($modelsSubject)) ? [new EducationProgrammSubject] : $modelsSubject,
                'modelsTime' => (empty($modelsTime)) ? [[new EducationProgrammSubjectTime]] : $modelsTime,
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

        $modelsSubject = $model->programmSubject;
        $modelsTime = [];
        $oldTimes = [];

        if (!empty($modelsSubject)) {
            foreach ($modelsSubject as $index => $modelSubject) {
                $times = $modelSubject->educationProgrammSubjectTimes;
                $modelsTime[$index] = $times;
                $oldTimes = ArrayHelper::merge(ArrayHelper::index($times, 'id'), $oldTimes);
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            // reset
            $modelsTime = [];

            $oldSubjectIDs = ArrayHelper::map($modelsSubject, 'id', 'id');
            $modelsSubject = Model::createMultiple(EducationProgrammSubject::class, $modelsSubject);
            Model::loadMultiple($modelsSubject, Yii::$app->request->post());
            $deletedSubjectIDs = array_diff($oldSubjectIDs, array_filter(ArrayHelper::map($modelsSubject, 'id', 'id')));

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsSubject) && $valid;

            $timesIDs = [];
            if (isset($_POST['EducationProgrammSubjectTime'][0][0])) {
                foreach ($_POST['EducationProgrammSubjectTime'] as $index => $times) {
                    $timesIDs = ArrayHelper::merge($timesIDs, array_filter(ArrayHelper::getColumn($times, 'id')));
                    foreach ($times as $indexTime => $time) {
                        $data['EducationProgrammSubjectTime'] = $time;
                        $modelTime = (isset($time['id']) && isset($oldTimes[$time['id']])) ? $oldTimes[$time['id']] : new EducationProgrammSubjectTime;
                        $modelTime->load($data);
                        $modelsTime[$index][$indexTime] = $modelTime;
                        $valid = $modelTime->validate();
                    }
                }
            }

            $oldTimesIDs = ArrayHelper::getColumn($oldTimes, 'id');
            $deletedTimesIDs = array_diff($oldTimesIDs, $timesIDs);

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {

                        if (! empty($deletedTimesIDs)) {
                            EducationProgrammSubjectTime::deleteAll(['id' => $deletedTimesIDs]);
                        }

                        if (! empty($deletedSubjectIDs)) {
                            EducationProgrammSubject::deleteAll(['id' => $deletedSubjectIDs]);
                        }

                        foreach ($modelsSubject as $index => $modelSubject) {

                            if ($flag === false) {
                                break;
                            }

                            $modelSubject->programm_id = $model->id;

                            if (!($flag = $modelSubject->save(false))) {
                                break;
                            }

                            if (isset($modelsTime[$index]) && is_array($modelsTime[$index])) {
                                foreach ($modelsTime[$index] as $indexTime => $modelTime) {
                                    $modelTime->programm_subject_id = $modelSubject->id;
                                    if (!($flag = $modelTime->save(false))) {
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return  $this->getSubmitAction($model);
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
            'modelsSubject' => (empty($modelsSubject)) ? [new EducationProgrammSubject] : $modelsSubject,
            'modelsTime' => (empty($modelsTime)) ? [[new EducationProgrammSubjectTime]] : $modelsTime,
            'readonly' => $readonly
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
        $data = new EducationProgrammHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
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