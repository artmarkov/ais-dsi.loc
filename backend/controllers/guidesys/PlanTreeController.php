<?php

namespace backend\controllers\guidesys;

class PlanTreeController extends MainController
{
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        return $this->render('index');
    }
}
