<?php

namespace backend\controllers\teachers;

use Yii;
use backend\controllers\DefaultController;

/**
 * CostController implements the CRUD actions for common\models\teachers\Cost model.
 */
class CostController extends DefaultController 
{
    public $modelClass       = 'common\models\teachers\Cost';
    public $modelSearchClass = 'common\models\teachers\search\CostSearch';

    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'update':
                return ['update', 'id' => $model->id];
                break;
            case 'create':
                return ['update', 'id' => $model->id];
                break;
            default:
                return parent::getRedirectPage($action, $model);
        }
    }
}