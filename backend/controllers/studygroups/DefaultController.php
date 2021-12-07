<?php
/**
 * Created by PhpStorm.
 * User: Zver
 * Date: 05.10.2018
 * Time: 12:14
 */

namespace backend\controllers\studygroups;

use artsoft\models\User;
use backend\models\Model;
use common\models\studygroups\SubjectSectStudyplan;
use common\models\studygroups\SubjectSect;
use common\models\studyplan\StudyplanSubject;
use common\models\user\UserCommon;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\studygroups\SubjectSect';
    public $modelSearchClass = 'common\models\studygroups\search\SubjectSectSearch';

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;

        $model = new $this->modelClass;
        $modelsDependence = [new SubjectSectStudyplan()];

        if ($model->load(Yii::$app->request->post())) {

            $modelsDependence = Model::createMultiple(SubjectSectStudyplan::class);
            Model::loadMultiple($modelsDependence, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsDependence) && $valid;
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsDependence as $modelDependence) {
                            $modelDependence->subject_sect_id = $model->id;
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

        return $this->renderIsAjax('create', [
            'model' => $model,
            'modelsDependence' => (empty($modelsDependence)) ? [new SubjectSectStudyplan()] : $modelsDependence,
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
            throw new NotFoundHttpException("The user was not found.");
        }

        $modelsDependence = $model->subjectSectStudyplans;
        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsDependence, 'id', 'id');
            $modelsDependence = Model::createMultiple(SubjectSectStudyplan::class, $modelsDependence);
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
                            SubjectSectStudyplan::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsDependence as $modelDependence) {
                            $modelDependence->subject_sect_id = $model->id;
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

        return $this->render('update', [
            'model' => $model,
            'modelsDependence' => (empty($modelsDependence)) ? [new SubjectSectStudyplan] : $modelsDependence,
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