<?php

namespace backend\controllers\subject;

use Yii;
use backend\controllers\DefaultController;

/**
 * TypeController implements the CRUD actions for common\models\subject\SubjectType model.
 */
class TypeController extends DefaultController 
{
    public $modelClass       = 'common\models\subject\SubjectType';
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