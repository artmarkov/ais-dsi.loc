<?php

namespace backend\controllers\execution;


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

        return $this->renderIsAjax('index');
    }


}
