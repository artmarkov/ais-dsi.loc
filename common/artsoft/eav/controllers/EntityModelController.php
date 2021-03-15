<?php

namespace artsoft\eav\controllers;


/**
 * EavEntityModelController implements the CRUD actions for artsoft\eav\models\EavEntityModel model.
 */
class EntityModelController extends \backend\controllers\DefaultController
{
    public $modelClass = 'artsoft\eav\models\EavEntityModel';
    public $modelSearchClass = 'artsoft\eav\models\search\EavEntityModelSearch';

    public $disabledActions = ['view', 'bulk-activate', 'bulk-deactivate'];

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