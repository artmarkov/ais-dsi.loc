<?php

namespace backend\controllers\teachers;

use Yii;
use backend\controllers\DefaultController;

/**
 * BonusItemController implements the CRUD actions for common\models\teachers\BonusItem model.
 */
class BonusItemController extends DefaultController 
{
    public $modelClass       = 'common\models\teachers\BonusItem';
    public $modelSearchClass = 'common\models\teachers\search\BonusItemSearch';

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