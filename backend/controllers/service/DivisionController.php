<?php

namespace backend\controllers\service;

use Yii;
use backend\controllers\DefaultController;

/**
 * DivisionController implements the CRUD actions for common\models\service\Division model.
 */
class DivisionController extends DefaultController 
{
    public $modelClass       = 'common\models\service\Division';
    public $modelSearchClass = '';

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