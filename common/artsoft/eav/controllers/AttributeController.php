<?php

namespace artsoft\eav\controllers;



/**
 * AttributeController implements the CRUD actions for artsoft\eav\models\EavAttribute model.
 */
class AttributeController extends \backend\controllers\DefaultController
{
    public $modelClass = 'artsoft\eav\models\EavAttribute';
    public $modelSearchClass = 'artsoft\eav\models\search\EavAttributeSearch';

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