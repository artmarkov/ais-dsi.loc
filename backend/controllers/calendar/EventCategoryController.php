<?php

namespace backend\controllers\calendar;

use Yii;
use backend\controllers\DefaultController;

/**
 * EventCategoryController implements the CRUD actions for common\models\calendar\EventCategory model.
 */
class EventCategoryController extends DefaultController 
{
    public $modelClass       = 'common\models\calendar\EventCategory';
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