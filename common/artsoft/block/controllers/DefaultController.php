<?php

namespace artsoft\block\controllers;

use artsoft\controllers\admin\BaseController;
use himiklab\sortablegrid\SortableGridAction;

/**
 * Controller implements the CRUD actions for Block model.
 */
class DefaultController extends BaseController
{
    public $modelClass = 'artsoft\block\models\Block';
    public $modelSearchClass = 'artsoft\block\models\search\BlockSearch';

     /**
     * action sort for himiklab\sortablegrid\SortableGridBehavior
     * @return type
     */
    public function actions()
    {
        return [
            'grid-sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => $this->modelClass,
            ],
        ];
    }
    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'index':
            case 'delete':
                return ['index'];
                break;
            case 'create':
                return ['create'];
                break;
            case 'update':
                return ['update', 'id' => $model->id];
                break;
            default:
                return ['index'];
        }
    }
}