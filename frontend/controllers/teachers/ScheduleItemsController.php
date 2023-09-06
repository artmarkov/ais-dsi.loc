<?php

namespace frontend\controllers\teachers;

use common\models\schedule\search\SubjectScheduleViewSearch;
use common\models\schedule\SubjectScheduleView;
use common\models\schedule\SubjectSchedule;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * ScheduleItemsController
 */
class ScheduleItemsController extends MainController
{
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->getMenu();
        $model_date = $this->modelDate;
        $model = Teachers::findOne($this->teachers_id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The Teachers was not found.");
        }
        $model_date = $this->modelDate;

        $query = SubjectScheduleView::find()->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($this->teachers_id)])->andWhere(['=', 'plan_year', $model_date->plan_year]);
        $searchModel = new SubjectScheduleViewSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel', 'model_date', 'model'));

    }

    public function actionCreate()
    {
        if (!Yii::$app->request->get('load_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET load_id.");
        }
        $teachersLoadModel = TeachersLoad::findOne(Yii::$app->request->get('load_id'));
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schedule Items'), 'url' => ['teachers/schedule-items']];
        $this->view->params['breadcrumbs'][] = 'Добавление расписания';
        $model = new SubjectSchedule();
        $model->teachers_load_id = Yii::$app->request->get('load_id');
        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
            $this->getSubmitAction($model);
        }

        return $this->renderIsAjax('@backend/views/schedule/default/_form.php', [
            'model' => $model,
            'teachersLoadModel' => $teachersLoadModel,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = SubjectSchedule::findOne($id);
        $teachersLoadModel = TeachersLoad::findOne($model->teachers_load_id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The SubjectSchedule was not found.");
        }

        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            $this->getSubmitAction($model);
        }
        return $this->renderIsAjax('@backend/views/schedule/default/_form.php', [
            'model' => $model,
            'teachersLoadModel' => $teachersLoadModel,
        ]);
    }

    public function actionDelete($id = null)
    {
        if (!Yii::$app->request->get('objectId')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET objectId.");
        }
        $objectId = Yii::$app->request->get('objectId');
        $model = SubjectSchedule::findOne($objectId);
        $model->delete();

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));
    }

    public function actionSchedule()
    {
        $model = Teachers::findOne($this->teachers_id);
        $this->view->params['breadcrumbs'][] = 'Расписание занятий(график)';
        $this->view->params['tabMenu'] = $this->getMenu();

        if (!isset($model)) {
            throw new NotFoundHttpException("The Teachers was not found.");
        }
        $model_date = $this->modelDate;

        return $this->render('schedule', [
            'model' => $model,
            'model_date' => $model_date,
            'readonly' => true
        ]);
    }

    public function getMenu()
    {
        return [
            ['label' => 'Злементы расписания', 'url' => ['/teachers/schedule-items/index']],
            ['label' => 'Расписание занятий', 'url' => ['/teachers/schedule-items/schedule']],
        ];
    }
}