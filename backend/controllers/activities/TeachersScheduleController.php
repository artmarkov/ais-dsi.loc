<?php

namespace backend\controllers\activities;

use common\models\user\UserCommon;
use common\widgets\fullcalendarscheduler\src\models\Resource;
use common\widgets\fullcalendarscheduler\src\models\Event as BaseEvent;
use yii\db\Query;
use yii\web\Response;
use Yii;

class TeachersScheduleController extends MainController
{
    public $modelClass = 'common\models\activities\ActivitiesTeachersView';

    /**
     * @return mixed|string
     */
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        return $this->render('index');
    }

    /**
     * формирует массив событий текущей страницы календаря
     * @return array
     */
    public function actionInitCalendar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');
        $start_time = Yii::$app->formatter->asTimestamp($start);
        $end_time = Yii::$app->formatter->asTimestamp($end);

        $events = $this->modelClass::find()
            ->where(
                "start_time > :start_time and end_time < :end_time",
                [
                    ":start_time" => $start_time,
                    ":end_time" => $end_time
                ]
            )
            ->orderBy('start_time')
            ->all();
        $tasks = [];
        foreach ($events as $item) {

            $event = new BaseEvent();
            $event->id = $item->id;
            $event->title = $item->title;
            $event->end = BaseEvent::getDate($item->end_time);
            $event->start = BaseEvent::getDate($item->start_time);

            $event->color = BaseEvent::getBgColor($item->color);
            $event->textColor = BaseEvent::getColor($item->color);
            $event->borderColor = BaseEvent::getBorderColor($item->color);
            $event->display = $item->rendering != 0 ? BaseEvent::RENDERING_BACKGROUND : null; // для фоновых событий

            //$event->url = Url::to(['/activities/default/view/', 'id' => $item->id]); // ссылка для просмотра события - перебивает событие по клику!!!
            $event->allDay = $item->allDay;
            $event->resourceId = $item->teachers_id;
            $tasks[] = $event;
        }

//        echo '<pre>' . print_r($tasks, true) . '</pre>';

        return $tasks;
    }

    /**
     * @return array
     */
    public function actionTeachers()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $this->view->params['tabMenu'] = $this->tabMenu;
        $teachers = (new Query())->from('teachers_view')->where(['=', 'status', UserCommon::STATUS_ACTIVE])->orderBy(['fullname' => SORT_ASC])->all();
        $tasks = [];
        foreach ($teachers as $item) {
            $resource = new Resource();
            $resource->id = $item['teachers_id'];
            $resource->parent = false;
            $resource->title = $item['fullname'];
            $tasks[] = $resource;
        }
//        echo '<pre>' . print_r($events, true) . '</pre>';
        return $tasks;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreateEvent()
    {

        $eventData = Yii::$app->request->post('eventData');
        $id = $eventData['id'];

        if ($id == 0) {
            $model = new $this->modelClass();

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('art', 'Your item has been created.'));
                return $this->redirect('/admin/activities/schedule/index');
            }
            $model->getData($eventData);
        } else {
            $model = $this->modelClass::findOne($id);
        }
        return $this->renderAjax('schedule-modal', [
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
            return $this->redirect('/admin/activities/schedule/index');
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
        return $this->redirect('/admin/activities/schedule/index');
    }
}