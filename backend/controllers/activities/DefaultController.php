<?php

namespace backend\controllers\activities;

use artsoft\widgets\ActiveForm;
use yii\web\Response;
use Yii;

class DefaultController extends MainController
{
//    public $modelClass = 'common\models\activities\Activities';
//    public $modelSearchClass = 'common\models\activities\search\ActivitiesSearch';

    public function actionCalendar()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->title = 'Календарь мероприятий';
        $this->view->params['breadcrumbs'][] = $this->view->title;
//        $dataProvider = new ActiveDataProvider([
//            'query' => $this->modelClass::find()
//        ]);

        return $this->render('calendar.php');
    }
}