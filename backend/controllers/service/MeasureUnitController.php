<?php

namespace backend\controllers\service;

use Yii;
use backend\controllers\DefaultController;

/**
 * LevelController implements the CRUD actions for common\models\service\measure-unit model.
 */
class MeasureUnitController extends DefaultController 
{
    public $modelClass       = 'common\models\service\MeasureUnit';
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