<?php

namespace frontend\controllers\teachers;

use common\models\schedule\ConsultSchedule;
use common\models\schedule\ConsultScheduleConfirm;
use common\models\schedule\ConsultScheduleView;
use common\models\schedule\search\ConsultScheduleViewSearch;
use common\models\teachers\Teachers;
use common\models\teachers\TeachersLoad;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * ConsultItemsController
 */
class ConsultItemsController extends MainController
{
    public function actionIndex()
    {
        $model_date = $this->modelDate;
        $modelTeachers = Teachers::findOne($this->teachers_id);

        $query = ConsultScheduleView::find()->where(['=', 'teachers_id', $this->teachers_id])
            ->andWhere(['status' => 1])
            ->andWhere(['=', 'plan_year', $model_date->plan_year]);
        $searchModel = new ConsultScheduleViewSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        $model_confirm = ConsultScheduleConfirm::find()->where(['=', 'teachers_id', $this->teachers_id])->andWhere(['=', 'plan_year', $model_date->plan_year])->one() ?? new ConsultScheduleConfirm();
        $model_confirm->teachers_id = $this->teachers_id;
        $model_confirm->plan_year = $model_date->plan_year;
        if ($model_confirm->load(Yii::$app->request->post()) && $model_confirm->validate()) {
            if (Yii::$app->request->post('submitAction') == 'send_approve') {
                $model_confirm->confirm_status = ConsultScheduleConfirm::DOC_STATUS_WAIT;
                if ($model_confirm->sendApproveMessage()) {
                    Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                }
            } elseif (Yii::$app->request->post('submitAction') == 'make_changes') {
                $model_confirm->confirm_status = ConsultScheduleConfirm::DOC_STATUS_MODIF;
            }
            if ($model_confirm->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction();
            }
        }

        return $this->renderIsAjax('consult-items', compact('dataProvider', 'searchModel', 'model_date', 'modelTeachers', 'model_confirm'));

    }

    public function actionCreate()
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['teachers/consult-items']];
        $this->view->params['breadcrumbs'][] = 'Добавление нагрузки';
        if (!Yii::$app->request->get('load_id')) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр GET load_id.");
        }
        $teachersLoadModel = TeachersLoad::findOne(Yii::$app->request->get('load_id'));
        $model = new ConsultSchedule();
        $model->teachers_load_id = Yii::$app->request->get('load_id');
        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been created.'));
            if (Yii::$app->request->post('submitAction') == 'savenext') {
                $this->redirect(['/teachers/consult-items/create', 'load_id' => $model->teachers_load_id]);
            } else {
                $this->getSubmitAction($model);
            }
        }

        return $this->renderIsAjax('@backend/views/schedule/consult-schedule/_form.php', [
            'model' => $model,
            'teachersLoadModel' => $teachersLoadModel,
        ]);
    }

    public function actionUpdate($id)
    {
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedule'), 'url' => ['teachers/consult-items']];
        $this->view->params['breadcrumbs'][] = 'Редактирование нагрузки';

        $model = ConsultSchedule::findOne($id);
        $teachersLoadModel = TeachersLoad::findOne($model->teachers_load_id);
        if (!isset($model)) {
            throw new NotFoundHttpException("The SubjectSchedule was not found.");
        }

        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
            if (Yii::$app->request->post('submitAction') == 'savenext') {
                $this->redirect(['/teachers/consult-items/create', 'load_id' => $model->teachers_load_id]);
            } else {
                $this->getSubmitAction($model);
            }
        }

        return $this->renderIsAjax('@backend/views/schedule/consult-schedule/_form.php', [
            'model' => $model,
            'teachersLoadModel' => $teachersLoadModel,
        ]);

    }

    public function actionDelete($id)
    {
        $model = ConsultSchedule::findOne($id);
        $model->delete();

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect($this->getRedirectPage('delete', $model));
    }
}