<?php

namespace backend\controllers\service;

use Yii;
use backend\controllers\DefaultController;

/**
 * TreeItemController implements the CRUD actions for common\models\service\TreeItem model.
 */
class TreeItemController extends DefaultController 
{
    public $modelClass       = 'common\models\service\TreeItem';
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
    public function actionIndex()
    {
        return $this->render('index');
    }
}