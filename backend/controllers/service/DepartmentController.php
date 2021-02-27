<?php

namespace backend\controllers\service;

use Yii;
use backend\controllers\DefaultController;

/**
 * DepartmentController implements the CRUD actions for common\models\service\Department model.
 */
class DepartmentController extends DefaultController 
{
    public $modelClass       = 'common\models\service\Department';
    public $modelSearchClass = 'common\models\service\search\DepartmentSearch';

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