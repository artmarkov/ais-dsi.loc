<?php

namespace frontend\controllers\teachers;

use backend\models\Model;
use common\models\studyplan\search\ThematicViewSearch;
use common\models\studyplan\StudyplanThematic;
use common\models\studyplan\StudyplanThematicItems;
use common\models\teachers\Teachers;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

/**
 * ThematicItemsController
 */
class ThematicItemsController extends MainController
{
    public $modelClass = 'common\models\studyplan\StudyplanThematic';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanThematicSearch';

    public function actionIndex()
    {
        $model_date = $this->modelDate;
        $model = Teachers::findOne($this->teachers_id);
        $searchModel = new ThematicViewSearch();

        $searchName = StringHelper::basename($searchModel::className());
        $params = Yii::$app->request->getQueryParams();
        $params[$searchName]['status'] = 1;
        $params[$searchName]['teachers_id'] = $this->teachers_id;
        $params[$searchName]['plan_year'] = $model_date->plan_year;
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('thematic-items', compact('dataProvider', 'searchModel',  'model_date', 'model'));

    }


    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['teachers/thematic-items/index']];
        $this->view->params['breadcrumbs'][] = 'Добавление плана';

        if (!Yii::$app->request->get('studyplan_subject_id') && !Yii::$app->request->get('subject_sect_studyplan_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_subject_id или subject_sect_studyplan_id.");
        }

        $model = new StudyplanThematic();
        $modelsItems = [new StudyplanThematicItems()];

        $model->studyplan_subject_id = Yii::$app->request->get('studyplan_subject_id') ?? 0;
        $model->subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id') ?? 0;

        if ($model->load(Yii::$app->request->post())) {

            $modelsItems = Model::createMultiple(StudyplanThematicItems::class, $modelsItems);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItems) && $valid;
            //$valid = true;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {

                    if ($flag = $model->save(false)) {
                        foreach ($modelsItems as $modelItems) {
                            $modelItems->studyplan_thematic_id = $model->id;
                            if (!($flag = $modelItems->save(false))) {
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

        return $this->renderIsAjax('@backend/views/studyplan/studyplan-thematic/_form.php', [
            'model' => $model,
            'modelsItems' => (empty($modelsItems)) ? [new StudyplanThematicItems] : $modelsItems,
            'readonly' => false
        ]);
    }

    public function actionView($id)
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['teachers/thematic-items/index']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $id);
        $model = StudyplanThematic::findOne($id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanThematic was not found.");
        }
        $modelsItems = $model->studyplanThematicItems;

        return $this->renderIsAjax('@backend/views/studyplan/studyplan-thematic/_form.php', [
            'model' => $model,
            'modelsItems' => (empty($modelsItems)) ? [new StudyplanThematicItems] : $modelsItems,
            'readonly' => true
        ]);
    }

    public function actionUpdate($id)
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['teachers/thematic-items/index']];
        $this->view->params['breadcrumbs'][] = sprintf('#%06d', $id);
        $model = StudyplanThematic::findOne($id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The StudyplanThematic was not found.");
        }
        $modelsItems = $model->studyplanThematicItems;

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsItems, 'id', 'id');
            $modelsItems = Model::createMultiple(StudyplanThematicItems::class, $modelsItems);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsItems, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItems) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            StudyplanThematicItems::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsItems as $modelItems) {

                            $modelItems->studyplan_thematic_id = $model->id;
                            if (!($flag = $modelItems->save(false))) {
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

        return $this->renderIsAjax('@backend/views/studyplan/studyplan-thematic/_form.php', [
            'model' => $model,
            'modelsItems' => (empty($modelsItems)) ? [new StudyplanThematicItems] : $modelsItems,
            'readonly' => false
        ]);
    }

    public function actionDelete($id)
    {
        $model = StudyplanThematic::findOne($id);
        $model->delete();

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));
    }
}