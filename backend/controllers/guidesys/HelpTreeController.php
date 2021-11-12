<?php

namespace backend\controllers\guidesys;


/**
 * DefaultController implements the CRUD actions for common\models\guidesys\HelpTree model.
 */
class HelpTreeController extends MainController
{
    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        return $this->render('index');
    }
}
