<?php

namespace backend\controllers\execution;

use common\models\execution\ExecutionSchedule;

/**
 * Class DefaultController
 * @package backend\controllers\execution
 */

class DefaultController extends MainController
{
    /**
     * @return mixed|string|\yii\web\Response
     */
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $this->view->params['breadcrumbs'][] = 'Расписания на подпись';

        $model_date = $this->modelDate;
        $models = ExecutionSchedule::getData($model_date);
        $model = $models->getDataTeachers();
        return $this->renderIsAjax('index', compact('model','model_date'));
    }

}
