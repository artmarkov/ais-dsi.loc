<?php

namespace backend\controllers\teachers;

use Yii;
use backend\controllers\DefaultController;

/**
 * PositionController implements the CRUD actions for common\models\teachers\Position model.
 */
class PositionController extends DefaultController 
{
    public $modelClass       = 'common\models\teachers\Position';
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