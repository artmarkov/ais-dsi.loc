<?php

namespace frontend\controllers\activities;

use common\models\activities\Activities;
use common\models\activities\search\ActivitiesSearch;
use common\models\schoolplan\Schoolplan;
use common\widgets\fullcalendar\src\models\Event as BaseEvent;
use yii\base\DynamicModel;
use yii\helpers\Url;
use yii\web\Response;
use Yii;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\activities\Activities';
    public $modelSearchClass = 'common\models\activities\search\ActivitiesSearch';

    public function init()
    {
        $this->viewPath = '@backend/views/activities/default';

        parent::init();
    }

    public function actionIndex()
    {
        $session = Yii::$app->session;

        $model_date = new DynamicModel(['date']);
        $model_date->addRule(['date'], 'required')
            ->addRule(['date'], 'date');

        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {
            $day = date('d');
            $mon = date('m');
            $year = date('Y');

            $model_date->date = $session->get('_activities_date') ?? Yii::$app->formatter->asDate(mktime(0, 0, 0, $mon, $day, $year), 'php:d.m.Y');
        }
        $session->set('_activities_date', $model_date->date);
        $timestamp_in = Yii::$app->formatter->asTimestamp($model_date->date);
        $timestamp_out = $timestamp_in + 86400;

        $this->view->params['tabMenu'] = $this->tabMenu;

        $query = Activities::find()->where(['between', 'start_time', $timestamp_in, $timestamp_out]);
        $searchModel = new ActivitiesSearch($query);
        $params = Yii::$app->request->getQueryParams();
        $dataProvider = $searchModel->search($params);
        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'model_date'));
    }

    public function actionView($id)
    {
        $resource = Yii::$app->request->get('resource');
        $this->view->params['tabMenu'] = $this->tabMenu;
        return $this->renderIsAjax('view', [
            'model' => Activities::find()->where(['id' => $id])->andWhere(['resource' => $resource])->one(),
        ]);
    }

    /**
     * @return string|\yii\web\Response
     *
     * рендерим виджет календаря
     *
     */
    public function actionCalendar()
    {
        $session = Yii::$app->session;

        $model_date = new DynamicModel(['auditory_id']);
        $model_date->addRule(['auditory_id'], 'required');
        $this->view->params['tabMenu'] = $this->tabMenu;
        if (!($model_date->load(Yii::$app->request->post()) && $model_date->validate())) {

            $model_date->auditory_id = $session->get('_calendar-auditory') ?? '';
        }
        $session->set('_calendar-auditory', $model_date->auditory_id);

        return $this->render('calendar', [
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
        $auditory_id = Yii::$app->request->get('auditory_id');

        $start_time = Yii::$app->formatter->asTimestamp($start);
        $end_time = Yii::$app->formatter->asTimestamp($end);

        $events = $this->modelClass::find()
            ->where(
                "start_time > :start_time and end_time < :end_time and auditory_id = :auditory_id",
                [
                    ":start_time" => $start_time,
                    ":end_time" => $end_time,
                    ":auditory_id" => $auditory_id
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