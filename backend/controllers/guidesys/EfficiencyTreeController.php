<?php

namespace backend\controllers\guidesys;

class EfficiencyTreeController extends MainController
{
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        return $this->render('index');
    }
}
