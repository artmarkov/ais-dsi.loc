<?php

namespace backend\controllers\calendar;

use common\models\calendar\Event;
use edofre\fullcalendarscheduler\models\Event as BaseEvent;
use common\models\auditory\Auditory;
use Yii;
use backend\controllers\DefaultController;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use edofre\fullcalendarscheduler\models\Resource;

/**
 * EventController implements the CRUD actions for common\models\calendar\Event model.
 */
class EventController extends DefaultController
{
    public $modelClass = 'common\models\calendar\Event';
    public $modelSearchClass = '';

    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'update':
                return ['update', 'id' => $model->id];
                break;
            case 'create':
                return ['update', 'id' => $model->id];
                break;
            default:
                return parent::getRedirectPage($action, $model);
        }
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['refactor-event', 'remove-event'],
                ],
            ],
        ]);
    }
    /**
     * @return string|\yii\web\Response
     *
     * рендерим виджет календаря
     *
     */
    public function actionIndex()
    {
        return $this->renderIsAjax('index');
    } 
    /**
     * @return string|\yii\web\Response
     *
     * рендерим виджет календаря - расписания по аудиториям
     *
     */
    public function actionSchedule()
    {
        return $this->renderIsAjax('schedule');
    }
    /**
     * @return string|\yii\web\Response
     *
     * открывает модальное окно добавления события
     *
     */
    public function actionInitEvent()
    {

        $eventData = Yii::$app->request->post('eventData');
        $id = $eventData['id'];

        if ($id == 0) {
            $model = new Event();
            $model->start_timestamp = \Yii::$app->formatter->asDatetime($eventData['start']);
            $model->end_timestamp = \Yii::$app->formatter->asDatetime($eventData['end']);
            $model->auditory_id = $eventData['resourceId'];
            return $this->renderAjax('event-modal', [
                'model' => $model
            ]);

        } else {
            $model = Event::findOne($id);
            $model->start_timestamp = \Yii::$app->formatter->asDatetime($model->start_timestamp);
            $model->end_timestamp = \Yii::$app->formatter->asDatetime($model->end_timestamp);

            return $this->renderAjax('event-modal', [
                'model' => $model, 'id' => $id
            ]);
        }

    }

    /**
     * @param null $start
     * @param null $end
     *
     * формирует массив событий текущей страницы календаря
     *
     * @return array
     */
    public function actionCalendar()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');

        $start_timestamp = Yii::$app->formatter->asTimestamp($start);
        $end_timestamp = Yii::$app->formatter->asTimestamp($end);

        $events = Event::find()
            ->where(
                "start_timestamp > :start_timestamp and end_timestamp < :end_timestamp",
                [
                    ":start_timestamp" => $start_timestamp,
                    ":end_timestamp" => $end_timestamp
                ]
            )
            ->orderBy('start_timestamp')
            ->all();
        $tasks = [];
        foreach ($events as $item) {

            $event = new BaseEvent();
            $event->id = $item->id;
            $event->title = $item->title;
            $event->resourceId = $item->auditory_id;
            $event->color = $item->categoryColor; 
            $event->textColor = $item->categoryTextColor;
            $event->borderColor = $item->categoryBorderColor;
            if($item->categoryRendering != 0)  $event->rendering = 'background'; // для фоновых событий
            
            // $event->url = Url::to(['/calendar/event/view', 'id' => $item->id]); // ссылка для просмотра события - перебивает событие по клику!!!
            $item->all_day == 1 ? $event->allDay = true : $event->allDay = false;

            $event->start = date("Y-m-d H:i", (integer)mktime(
                date("H", $item->start_timestamp),
                date("i", $item->start_timestamp),
                0,
                date("m", $item->start_timestamp),
                date("d", $item->start_timestamp),
                date("Y", $item->start_timestamp)
            ));
            $event->end = date("Y-m-d H:i", (integer)mktime(
                date("H", $item->end_timestamp),
                date("i", $item->end_timestamp),
                0,
                date("m", $item->end_timestamp),
                date("d", $item->end_timestamp),
                date("Y", $item->end_timestamp)
            ));
            $tasks[] = $event;
        }
        // echo '<pre>' . print_r($events, true) . '</pre>';

        return $tasks;
    }
    
 public function actionResources()
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $events = Auditory::find()
               
                ->all();
        $tasks = [];
        foreach ($events as $item) {

            $resource = new Resource();
            $resource->id = $item->id;
            $resource->building = $item->buildingName;
            $resource->title = $item->num . ' ' .$item->name;
           
            $tasks[] = $resource;
        }
         //echo '<pre>' . print_r($tasks, true) . '</pre>';
         return $tasks;
    }

    /**
     * @return bool
     */
    public function actionRefactorEvent()
    {
        $eventData = Yii::$app->request->post('eventData');
        $id = $eventData['id'];

       $id == 0 ?  $model = new Event() : $model = Event::findOne($id);

        $model->title = $eventData['title'];
        $model->start_timestamp = \Yii::$app->formatter->asTimestamp($eventData['start']);

        if(!empty($eventData['end'])) $model->end_timestamp = \Yii::$app->formatter->asTimestamp($eventData['end']);
        if(!empty($eventData['allDay'])) $eventData['allDay'] == 'false' ? $model->all_day = 0 : $model->all_day = 1;
        if(!empty($eventData['resourceId'])) $model->auditory_id = $eventData['resourceId'];
        if(!empty($eventData['category_id'])) $model->category_id = $eventData['category_id'];
        if(!empty($eventData['description']))$model->description = $eventData['description'];
       // echo '<pre>' . print_r($model, true) . '</pre>';

        if($model->save()) {
            return true;
        }
        else {
            return false;
        }
    }
    /**
     * @return bool
     */
    public function actionRemoveEvent()
    {
        $id = Yii::$app->request->post('id');

        $model = Event::findOne($id);
        // echo '<pre>' . print_r($model, true) . '</pre>';
        if ($model) {
            $model->delete();
            return true;
        } else {
            return false;
        }
    }
}

