<?php

namespace backend\controllers\routine;

use artsoft\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use Yii;

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
            'query' => $this->modelClass::find(),
            'pagination' => false,
        ]);

        return $this->render('calendar', [
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

    /**
     * @return array|Response
     */
    public function actionCreateEvent()
    {
        $model = new $this->modelClass();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->save()):
                return $this->redirect('calendar');
            else:
                return ActiveForm::validate($model);
            endif;
        }
    }
}