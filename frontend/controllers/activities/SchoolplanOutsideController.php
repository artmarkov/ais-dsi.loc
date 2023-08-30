<?php

namespace frontend\controllers\activities;

use common\models\activities\ActivitiesCat;
use common\models\schoolplan\Schoolplan;
use common\widgets\fullcalendarscheduler\src\models\Event as BaseEvent;
use Yii;

/**
 * SchoolplanOutsideController implements the CRUD actions for common\models\schoolplan\SchoolplanView model.
 */
class SchoolplanOutsideController extends MainController
{
    public $modelClass       = 'common\models\schoolplan\SchoolplanView';
    public $modelSearchClass = 'common\models\schoolplan\search\SchoolplanViewSearch';

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
                "datetime_in > :start_time and datetime_out < :end_time AND auditory_id IS NULL",
                [
                    ":start_time" => $start_time,
                    ":end_time" => $end_time
                ]
            )
            ->orderBy('datetime_in')
            ->all();
        $tasks = [];
        $color = ActivitiesCat::findOne(['id' => 1003])->color;
        foreach ($events as $item) {

            $event = new BaseEvent();
            $event->id = $item->id;
            $event->title = $item->title;
            $event->end = BaseEvent::getDate($item->datetime_out);
            $event->start = BaseEvent::getDate($item->datetime_in);

            $event->color = BaseEvent::getBgColor($color);
            $event->textColor = BaseEvent::getColor($color);
            $event->borderColor = BaseEvent::getBorderColor($color);
            $event->display =  null; // для фоновых событий

            //$event->url = Url::to(['/activities/default/view/', 'id' => $item->id]); // ссылка для просмотра события - перебивает событие по клику!!!
            $event->allDay = null;
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

        $model = Schoolplan::find()->where(['=', 'id', $id])->one();
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
}