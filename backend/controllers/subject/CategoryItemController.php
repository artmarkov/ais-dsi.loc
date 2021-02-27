<?php

namespace backend\controllers\subject;

use Yii;
use backend\controllers\DefaultController;

/**
 * CategoryItemController implements the CRUD actions for common\models\subject\SubjectCategoryItem model.
 */
class CategoryItemController extends DefaultController 
{
    public $modelClass       = 'common\models\subject\SubjectCategoryItem';
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