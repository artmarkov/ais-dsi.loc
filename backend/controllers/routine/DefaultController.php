<?php
namespace backend\controllers\routine;

class DefaultController extends \backend\controllers\DefaultController
{
    public function actionIndex()
    {
        $this->view->title = 'Производственный календарь';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('index.php');
    }
}