<?php

namespace backend\controllers\preregistration;

use common\models\education\EntrantPreregistrations;
use Yii;

/**
 * DefaultController implements the CRUD actions for common\models\education\EntrantPreregistrations model.
 */
class SummaryController extends MainController
{

    public function actionIndex()
    {
        $this->view->params['tabMenu'] = $this->tabMenu;
        $model_date = $this->modelDate;

        $models = EntrantPreregistrations::getRegSummary($model_date);

        return $this->renderIsAjax('index', compact( 'models', 'model_date'));
    }
}