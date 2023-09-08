<?php

namespace backend\controllers\thematic;

use backend\models\Model;
use common\models\education\EducationProgrammLevel;
use common\models\education\LessonItems;
use common\models\education\LessonProgress;
use common\models\education\LessonProgressView;
use common\models\history\ConsultScheduleHistory;
use common\models\history\LessonItemsHistory;
use common\models\history\StudyplanHistory;
use common\models\history\StudyplanInvoicesHistory;
use common\models\history\SubjectCharacteristicHistory;
use common\models\history\SubjectScheduleHistory;
use common\models\history\TeachersLoadHistory;
use common\models\schedule\ConsultSchedule;
use common\models\schedule\search\ConsultScheduleStudyplanViewSearch;
use common\models\schoolplan\SchoolplanProtocolItems;
use common\models\schoolplan\search\SchoolplanProtocolItemsViewSearch;
use common\models\students\Student;
use common\models\studyplan\search\StudyplanInvoicesViewSearch;
use common\models\studyplan\search\StudyplanThematicViewSearch;
use common\models\studyplan\search\SubjectCharacteristicViewSearch;
use common\models\studyplan\search\ThematicViewSearch;
use common\models\studyplan\StudyplanInvoices;
use common\models\studyplan\StudyplanThematic;
use common\models\studyplan\StudyplanThematicItems;
use common\models\studyplan\SubjectCharacteristic;
use common\models\schedule\search\SubjectScheduleStudyplanViewSearch;
use common\models\schedule\SubjectSchedule;
use common\models\studyplan\Studyplan;
use common\models\studyplan\StudyplanSubject;
use common\models\subject\SubjectForm;
use common\models\subject\SubjectType;
use common\models\teachers\search\TeachersLoadStudyplanViewSearch;
use common\models\teachers\TeachersLoad;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\studyplan\StudyplanThematic';
    public $modelSearchClass = 'common\models\studyplan\search\StudyplanThematicSearch';

    public function actionIndex()
    {
        $model_date = $this->modelDate;

        $searchModel = new ThematicViewSearch();

        $searchName = StringHelper::basename($searchModel::className());
        $params = Yii::$app->request->getQueryParams();
        $params[$searchName]['teachers_id'] = $model_date->teachers_id;
        $params[$searchName]['plan_year'] = $model_date->plan_year;
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date'));
    }

    /**
     * @return mixed|string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['thematic/default/index']];
        $this->view->params['breadcrumbs'][] = Yii::t('art', 'Create');
        $this->view->params['tabMenu'] = $this->tabMenu;

        if (!Yii::$app->request->get('studyplan_subject_id') && !Yii::$app->request->get('subject_sect_studyplan_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET studyplan_subject_id или subject_sect_studyplan_id.");
        }

        $model = new StudyplanThematic();
        $modelsItems = [new StudyplanThematicItems()];

        $model->studyplan_subject_id = Yii::$app->request->get('studyplan_subject_id') ?? 0;
        $model->subject_sect_studyplan_id = Yii::$app->request->get('subject_sect_studyplan_id') ?? 0;

        if ($model->load(Yii::$app->request->post())) {
            $modelsItems = Model::createMultiple(StudyplanThematicItems::class);
            Model::loadMultiple($modelsItems, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsItems) && $valid;
            $valid = true;
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
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['studyplan/default/thematic-items', 'id' => $model->id]];
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
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Thematic plans'), 'url' => ['studyplan/default/thematic-items', 'id' => $model->id]];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['studyplan/default/update', 'id' => $model->id]];
        $data = new StudyplanThematicHistory($id);
        return $this->renderIsAjax('/studyplan/default/history', compact(['model', 'data']));

    }

    public function actionDelete($id)
    {
        $model = StudyplanThematic::findOne($id);
        $model->delete();

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));

    }

    public function actionThematicItems($id, $objectId = null, $mode = null, $readonly = false)
    {
        $model = $this->findModel($id);
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Individual plans'), 'url' => ['studyplan/default/index']];
        $this->view->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $id), 'url' => ['studyplan/default/view', 'id' => $id]];

        if ('create' == $mode) {



        } elseif ('history' == $mode && $objectId) {

        } elseif ('delete' == $mode && $objectId) {
             $this->redirect($this->getRedirectPage('delete', $model));

        } elseif ($objectId) {
            if ('view' == $mode) {
                $readonly = true;
            }


        } else {
           }
    }
}