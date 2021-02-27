<?php

namespace backend\controllers\teachers;

use Yii;
use backend\controllers\DefaultController;

/**
 * BonusCategoryController implements the CRUD actions for common\models\teachers\BonusCategory model.
 */
class BonusCategoryController extends DefaultController 
{
    public $modelClass       = 'common\models\teachers\BonusCategory';
    public $modelSearchClass = 'common\models\teachers\search\BonusCategorySearch';

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