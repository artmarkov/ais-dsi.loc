<?php

namespace backend\controllers\sect;

use artsoft\helpers\ArtHelper;
use artsoft\models\OwnerAccess;
use artsoft\models\User;
use backend\models\Model;
use common\models\studyplan\search\StudyplanSearch;
use common\models\subjectsect\search\SubjectSectScheduleSearch;
use common\models\subjectsect\SubjectSectSchedule;
use common\models\subjectsect\SubjectSectStudyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\StringHelper;
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

        return $this->renderIsAjax($this->createView, [
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
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id);

        if (!isset($model)) {
            throw new NotFoundHttpException("The SubjectSect was not found.");
        }

        $modelsSubjectSectStudyplan = $model->subjectSectStudyplans;
        $modelsTeachersLoad = [];
        $oldTimes = [];

        if (!empty($modelsSubjectSectStudyplan)) {
            foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan) {
                $times = $modelSubjectSectStudyplan->teachersLoads;
                $modelsTeachersLoad[$index] = $times;
                $oldTimes = ArrayHelper::merge(ArrayHelper::index($times, 'id'), $oldTimes);
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

            $timesIDs = [];
            if (isset($_POST['TeachersLoad'][0][0])) {
                foreach ($_POST['TeachersLoad'] as $index => $times) {
                    $timesIDs = ArrayHelper::merge($timesIDs, array_filter(ArrayHelper::getColumn($times, 'id')));
                    foreach ($times as $indexTime => $time) {
                        $data['TeachersLoad'] = $time;
                        $modelTeachersLoad = (isset($time['id']) && isset($oldTimes[$time['id']])) ? $oldTimes[$time['id']] : new TeachersLoad;
                        $modelTeachersLoad->load($data);
                        $modelsTeachersLoad[$index][$indexTime] = $modelTeachersLoad;
                        $valid = $modelTeachersLoad->validate();
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
                            TeachersLoad::deleteAll(['id' => $deletedTimesIDs]);
                        }

                        if (!empty($deletedSubjectIDs)) {
                            SubjectSectStudyplan::deleteAll(['id' => $deletedSubjectIDs]);
                        }

                        foreach ($modelsSubjectSectStudyplan as $index => $modelSubjectSectStudyplan) {

                            if ($flag === false) {
                                break;
                            }
                            $modelSubjectSectStudyplan->subject_sect_id = $model->id;
                            if (!($flag = $modelSubjectSectStudyplan->save(false))) {
                                break;
                            }

                            $modelSubjectSectStudyplan = SubjectSectStudyplan::findOne(['id' => $modelSubjectSectStudyplan->id]);

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
                        return $this->getSubmitAction($model);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render($this->updateView, [
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

    public function actionSchedule($id)
    {
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->modelClass::findOne($id);
        $readonly = false;
        return $this->render('schedule', ['model' => $model,
            'readonly' => $readonly,
            ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionScheduleItems($id, $objectId = null, $mode = null)
    {
        $model = $this->findModel($id);
        $this->view->params['tabMenu'] = $this->getMenu($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Subject Sects'), 'url' => ['sect/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['sect/default/update', 'id' => $model->id]];

        if ('create' == $mode) {
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['sect/default/schedule-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = 'Добавление расписания';
            $model = new SubjectSectSchedule();
            $model->subject_sect_studyplan_id = Yii::$app->request->get('id') ?: null;

            if ($model->load(Yii::$app->request->post())) {

            }

            return $this->renderIsAjax('@backend/views/schedule/default/_form.php', [
                'model' => $model,
            ]);


        } elseif ('history' == $mode && $objectId) {
            $model = SubjectSectSchedule::findOne($objectId);
            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['sect/default/schedule-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['sect/default/update', 'id' => $model->id]];
            $data = new SubjectSectScheduleHistory($objectId);
            return $this->renderIsAjax('/sect/default/history', compact(['model', 'data']));

        } elseif ('delete' == $mode && $objectId) {
            $model = SubjectSectSchedule::findOne($objectId);
            $model->delete();

            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
            return $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {

            $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['sect/default/schedule-items', 'id' => $model->id]];
            $this->view->params['breadcrumbs'][] = sprintf('#%06d', $objectId);
            $model = SubjectSectSchedule::findOne($objectId);

            if (!isset($model)) {
                throw new NotFoundHttpException("The StudyplanSubject was not found.");
            }

            if ($model->load(Yii::$app->request->post())) {

            }

            return $this->renderIsAjax('@backend/views/schedule/default/_form.php', [
                'model' => $model,
            ]);

        } else {
            // $modelClass = 'common\models\subjectsect\SubjectSectSchedule';
            $searchModel = new SubjectSectScheduleSearch();

            $searchName = StringHelper::basename($searchModel::className());
            $params = Yii::$app->request->getQueryParams();
            $params[$searchName]['subject_sect_id'] = $id;
            $dataProvider = $searchModel->search($params);

            return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel'));
        }
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