<?php

namespace artsoft\logs\controllers;

use artsoft\models\Session;
use yii\data\ArrayDataProvider;

/**
 * RequestsController implements the CRUD actions for Requests model.
 */
class RequestController extends MainController
{
    /**
     *
     * @inheritdoc
     */
    public $modelClass = 'artsoft\models\Request';
    public $modelSearchClass = 'artsoft\logs\models\search\RequestSearch';
    public $disabledActions = ['view', 'create', 'update'];

    /**
     * @param string $action
     * @param null $model
     * @return array|string
     */
    protected function getRedirectPage($action, $model = null)
    {
        switch ($action) {
            case 'delete':
                return ['index'];
                break;
            default:
                return ['index'];
        }
    }
}