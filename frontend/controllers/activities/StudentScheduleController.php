<?php

namespace frontend\controllers\activities;

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\activities\ActivitiesStudyplanView;
use common\widgets\fullcalendar\src\models\Event as BaseEvent;
use yii\base\DynamicModel;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use Yii;

class StudentScheduleController extends MainController
{
    public $modelClass = 'common\models\activities\ActivitiesStudyplanView';
    public $student_id;

    public function init()
    {
        $this->viewPath = '@backend/views/activities/student-schedule';
        $this->student_id = false;
        if(User::hasRole(['student'])) {
        $userId = Yii::$app->user->identity->getId();
        $this->student_id = RefBook::find('users_students')->getValue($userId) ?? null;
        }
        parent::init();
    }
    /**
     * @return string|\yii\web\Response
     *
     * рендерим виджет календаря
     *
     */
    public function actionIndex()
    {
        $session = Yii::$app->session;

        $model_date = new DynamicModel(['student_id']);
        $model_date->addRule(['student_id'], 'required');
        $this->view->params['tabMenu'] = $this->tabMenu;
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {

            $model_date->student_id = $this->student_id ?? ($session->get('_calendar-student') ?? ActivitiesStudyplanView::getStudentScalar());
        }
        $session->set('_calendar-student', $model_date->student_id);

        return $this->render('index', [
            'model_date' => $model_date
        ]);
    }

    /**
     * @param null $start
     * @param null $end
     *
     * формирует массив событий текущей страницы календаря
     *
     * @return array
     */
    public function actionInitCalendar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');
        $student_id = Yii::$app->request->get('student_id');

        $start_time = Yii::$app->formatter->asTimestamp($start);
        $end_time = Yii::$app->formatter->asTimestamp($end);

        $events = $this->modelClass::find()
            ->where(
                "start_time > :start_time and end_time < :end_time and student_id = :student_id and (direction_id = 1000 or direction_id IS NULL)",
                [
                    ":start_time" => $start_time,
                    ":end_time" => $end_time,
                    ":student_id" => $student_id
                ]
            )
            ->orderBy('start_time')
            ->all();
        $tasks = [];
        foreach ($events as $item) {

            $event = new BaseEvent();
            $event->id = $item->id;
            $event->title = $item->title;
            $event->source = $item->resource;
            $event->end = BaseEvent::getDate($item->end_time);
            $event->start = BaseEvent::getDate($item->start_time);

            $event->color = BaseEvent::getBgColor($item->color);
            $event->textColor = BaseEvent::getColor($item->color);
            $event->borderColor = BaseEvent::getBorderColor($item->color);
            $event->display = $item->rendering != 0 ? BaseEvent::RENDERING_BACKGROUND : null; // для фоновых событий

            //$event->url = Url::to(['/activities/default/view/', 'id' => $item->id]); // ссылка для просмотра события - перебивает событие по клику!!!
            $event->allDay = $item->allDay;

            $tasks[] = $event;
        }

//        echo '<pre>' . print_r($tasks, true) . '</pre>';

        return $tasks;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreateEvent()
    {

        $eventData = Yii::$app->request->post('eventData');
//        print_r($eventData); die();
        $id = $eventData['id'];
        $resource = $eventData['resource'];
        $model = $this->modelClass::find()->where(['=', 'resource', $resource])->andWhere(['=', 'id', $id])->one();
//        if ($id == 0) {
//            $model = new $this->modelClass();
//
//            if ($model->load(Yii::$app->request->post()) && $model->save()) {
//                Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
//                return $this->redirect('/admin/activities/default/calendar');
//            }
//            $model->getData($eventData);
//        } else {
//            if ($resource == 'schoolplan') {
//                $model = Schoolplan::findOne($id);
//            }
//        }
        return $this->renderAjax('activities-modal', [
            'model' => $model
        ]);

    }

    public function actionRefactorEvent()
    {

        $eventData = Yii::$app->request->post('eventData');
        $id = $eventData['id'];
        $model = $this->modelClass::findOne($id);
        $model->getData($eventData);
        if ($model->save()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $id
     * @return array|Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdateEvent($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been updated.'));
            return $this->redirect('/admin/activities/default/calendar');
        }
    }

    /**
     * @param $id
     * @return Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDeleteEvent($id)
    {
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('info', Yii::t('art', 'Your item has been deleted.'));
        return $this->redirect('/admin/activities/default/calendar');
    }
}