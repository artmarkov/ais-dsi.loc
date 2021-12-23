<?php

namespace backend\controllers\sect;

use backend\models\Model;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\subjectsect\SubjectSect';
    public $modelSearchClass = 'common\models\subjectsect\search\SubjectSectSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;
        $modelsSubjectSectStudyplan = [new SubjectSectStudyplan()];
        $modelsTeachersLoad = [[new TeachersLoad()]];

        if ($model->load(Yii::$app->request->post())) {

            $modelsSubjectSectStudyplan = Model::createMultiple(SubjectSectStudyplan::class);
            Model::loadMultiple($modelsSubjectSectStudyplan, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsSubjectSectStudyplan) && $valid;
            //$valid = true;
            if (isset($_POST['TeachersLoad'][0][0])) {
                foreach ($_POST['TeachersLoad'] as $index => $times) {
                    foreach ($times as $indexTime => $time) {
                        $data['TeachersLoad'] = $time;
                        $modelTeachersLoad = new TeachersLoad;
                        $modelTeachersLoad->load($data);
                        $modelsTeachersLoad[$index][$indexTime] = $modelTeachersLoad;
                        $valid = $modelTeachersLoad->validate();
                    }
                }
            }
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {

                        foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan) {

                            if ($flag === false) {
                                break;
                            }
                            $modelSubjectSectStudyplan->subject_sect_id = $model->id;

                            if (!($flag = $modelSubjectSectStudyplan->save(false))) {
                                break;
                            }

                            if (isset($modelsTeachersLoad[$index]) && is_array($modelsTeachersLoad[$index])) {
                                foreach ($modelsTeachersLoad[$index] as $indexTime => $modelTeachersLoad) {
                                    $modelTeachersLoad->subject_sect_studyplan_id = $modelSubjectSectStudyplan->id;
                                    if (!($flag = $modelTeachersLoad->save(false))) {
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

        return $this->renderIsAjax('create', [
            'model' => $model,
            'modelsSubjectSectStudyplan' => (empty($modelsSubjectSectStudyplan)) ? [new SubjectSectStudyplan()] : $modelsSubjectSectStudyplan,
            'modelsTeachersLoad' => (empty($modelsTeachersLoad)) ? [[new TeachersLoad]] : $modelsTeachersLoad,
            'readonly' => false
        ]);
    }

    /**
     * @param int $id
     * @param bool $readonly
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id, $readonly = false)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The SubjectSect was not found.");
        }

        $modelsSubjectSectStudyplan = $model->subjectSectStudyplans;

        $modelsTeachersLoad = [];
        $oldLoads = [];

        if (!empty($modelsSubjectSectStudyplan)) {
            foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan) {
                $loads = $modelSubjectSectStudyplan->teachersLoad;
                $modelsTeachersLoad[$index] = $loads;
                $oldLoads = ArrayHelper::merge(ArrayHelper::index($loads, 'id'), $oldLoads);
            }
        }
        if ($model->load(Yii::$app->request->post())) {

            // reset
            $modelsTeachersLoad = [];

            $oldSubjectIDs = ArrayHelper::map($modelsSubjectSectStudyplan, 'id', 'id');
            $modelsSubjectSectStudyplan = Model::createMultiple(SubjectSectStudyplan::class, $modelsSubjectSectStudyplan);
            Model::loadMultiple($modelsSubjectSectStudyplan, Yii::$app->request->post());
            $deletedSubjectIDs = array_diff($oldSubjectIDs, array_filter(ArrayHelper::map($modelsSubjectSectStudyplan, 'id', 'id')));

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsSubjectSectStudyplan) && $valid;

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsSubjectSectStudyplan) && $valid;

            $timesIDs = [];
            if (isset($_POST['modelsTeachersLoad'][0][0])) {
                foreach ($_POST['modelsTeachersLoad'] as $index => $times) {
                    $timesIDs = ArrayHelper::merge($timesIDs, array_filter(ArrayHelper::getColumn($times, 'id')));
                    foreach ($times as $indexTime => $time) {
                        $data['modelsTeachersLoad'] = $time;
                        $modelsTeachersLoad = (isset($time['id']) && isset($oldTimes[$time['id']])) ? $oldLoads[$time['id']] : new TeachersLoad;
                        $modelsTeachersLoad->load($data);
                        $modelsTeachersLoad[$index][$indexTime] = $modelsTeachersLoad;
                        $valid = $modelsTeachersLoad->validate();
                    }
                }
            }

            $oldTimesIDs = ArrayHelper::getColumn($oldLoads, 'id');
            $deletedTimesIDs = array_diff($oldTimesIDs, $timesIDs);

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {

                        foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan) {

                            if ($flag === false) {
                                break;
                            }
                            $modelSubjectSectStudyplan->subject_sect_id = $model->id;

                            if (!($flag = $modelSubjectSectStudyplan->save(false))) {
                                break;
                            }

                            if (isset($modelsTeachersLoad[$index]) && is_array($modelsTeachersLoad[$index])) {
                                foreach ($modelsTeachersLoad[$index] as $indexTime => $modelTeachersLoad) {
                                    $modelTeachersLoad->subject_sect_studyplan_id = $modelSubjectSectStudyplan->id;
                                    if (!($flag = $modelTeachersLoad->save(false))) {
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

        return $this->render('update', [
            'model' => $model,
            'modelsSubjectSectStudyplan' => (empty($modelsSubjectSectStudyplan)) ? [new SubjectSectStudyplan] : $modelsSubjectSectStudyplan,
            'modelsTeachersLoad' => (empty($modelsTeachersLoad)) ? [[new TeachersLoad]] : $modelsTeachersLoad,
            'readonly' => $readonly
        ]);
    }

    /**
     * @param int $id
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView($id)
    {
        return $this->actionUpdate($id, true);
    }

    /**
     * @return false|string
     */
    public function actionSubject()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $union_id = $parents[0];
                $cat_id = $parents[1];
                $out = $this->modelClass::getSubjectForUnionAndCatToId($union_id, $cat_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    /**
     * @return false|string
     */
    public function actionSubjectCat()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if (!empty($parents)) {
                $union_id = $parents[0];
                $out = $this->modelClass::getSubjectCategoryForUnionToId($union_id);

                return json_encode(['output' => $out, 'selected' => '']);
            }
        }
        return json_encode(['output' => '', 'selected' => '']);
    }

    /**
     * Установка группы в инд планах
     * @return string|null
     * @throws \yii\db\Exception
     */
    public function actionSetGroup()
    {
        $studyplan_subject_id = $_GET['studyplan_subject_id'];

        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (isset($_POST['sect_id'][$studyplan_subject_id])) {
                $model = StudyplanSubject::findOne($studyplan_subject_id)->getSubjectSectStudyplan();
                $model->removeStudyplanSubject($studyplan_subject_id);

                $model = SubjectSectStudyplan::findOne($_POST['sect_id'][$studyplan_subject_id]);
                $model->insertStudyplanSubject($studyplan_subject_id);

                $value = $model->id;
                return Json::encode(['output' => $value, 'message' => '']);
            } else {
                return Json::encode(['output' => '', 'message' => '']);
            }
        }

        return null;
    }
}