<?php

namespace frontend\controllers\teachers;

use common\models\schedule\search\SubjectScheduleViewSearch;
use common\models\schedule\SubjectScheduleConfirm;
use common\models\schedule\SubjectScheduleView;
use common\models\schedule\SubjectSchedule;
use common\models\studyplan\StudyplanSubjectHist;
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

        $query = SubjectScheduleView::find()->where(['in', 'teachers_load_id', TeachersLoad::getTeachersSubjectAll($this->teachers_id)])
            ->andWhere(['=', 'plan_year', $model_date->plan_year])
            ->andWhere(['not in', 'studyplan_subject_id', StudyplanSubjectHist::getStudyplanSubjectPass()]);
        $searchModel = new SubjectScheduleViewSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);

        $model_confirm = SubjectScheduleConfirm::find()->where(['=', 'teachers_id', $this->teachers_id])->andWhere(['=', 'plan_year', $model_date->plan_year])->one() ?? new SubjectScheduleConfirm();
        $model_confirm->teachers_id = $this->teachers_id;
        $model_confirm->plan_year = $model_date->plan_year;
        if ($model_confirm->load(Yii::$app->request->post()) && $model_confirm->validate()) {
            if (Yii::$app->request->post('submitAction') == 'send_approve') {
                $model_confirm->confirm_status = SubjectScheduleConfirm::DOC_STATUS_WAIT;
                if ($model_confirm->sendApproveMessage()) {
                    Yii::$app->session->setFlash('info', Yii::t('art/mailbox', 'Your mail has been posted.'));
                }
            } elseif (Yii::$app->request->post('submitAction') == 'make_changes') {
                $model_confirm->confirm_status = SubjectScheduleConfirm::DOC_STATUS_MODIF;
            }
            if ($model_confirm->save()) {
                Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been updated.'));
                $this->getSubmitAction();
            }
        }

        return $this->renderIsAjax('schedule-items', compact('dataProvider', 'searchModel', 'model_date', 'model', 'model_confirm'));
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

    public function actionDelete($id)
    {
        $model = SubjectSchedule::findOne($id);
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
            ['label' => 'Элементы расписания', 'url' => ['/teachers/schedule-items/index']],
            ['label' => 'Расписание занятий', 'url' => ['/teachers/schedule-items/schedule']],
        ];
    }
}