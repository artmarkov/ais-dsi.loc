<?php

namespace backend\controllers\guidejob;

use common\models\history\CostHistory;

/**
 * CostController implements the CRUD actions for common\models\teachers\Cost model.
 */
class CostController extends MainController
{
    public $modelClass       = 'common\models\guidejob\Cost';
    public $modelSearchClass = 'common\models\guidejob\search\CostSearch';

    public function actionHistory($id)
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model = $this->findModel($id);
        $data = new CostHistory($id);
        return $this->renderIsAjax('history', compact(['model', 'data']));
    }
}