<?php

namespace backend\controllers\routine;

use common\models\routine\Routine;
use yii\data\ActiveDataProvider;

class DefaultController extends MainController
{
    public $modelClass = 'common\models\routine\Routine';
    public $modelSearchClass = 'common\models\routine\search\RoutineSearch';

    public function actionCalendar()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->title = 'Производственный календарь';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $dataProvider = new ActiveDataProvider([
            'query' => $this->modelClass::find()
//                ->joinWith('cat')->select('routine_cat.name as name, routine.name as location, start_date, end_date, routine.color as color')
        ]);

        return $this->render('calendar.php', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionInitEvent()
    {
        $model = new $this->modelClass();
        $model->start_date = \Yii::$app->request->post('startDate');
        $model->end_date = \Yii::$app->request->post('endDate');

        return $this->renderAjax('routine-modal', [
            'model' => $model
        ]);
    }
}