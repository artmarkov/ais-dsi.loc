<?php

namespace frontend\controllers\activities;

use Yii;

class ActivitiesOverController extends MainController
{
    public $modelClass       = 'common\models\activities\ActivitiesOver';
    public $modelSearchClass = 'common\models\activities\search\ActivitiesOverSearch';

    public function init()
    {
        $this->viewPath = '@backend/views/activities/activities-over';

        parent::init();
    }

    public function actionView($id, $readonly = true)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        /* @var $model \artsoft\db\ActiveRecord */
        $model = $this->findModel($id);

        return $this->render('update', [
            'model' => $model,
            'readonly' => $readonly
        ]);
    }

}