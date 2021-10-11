<?php

namespace artsoft\logs\controllers;


/**
 * SessionController implements the CRUD actions for Session model.
 */
class SessionController extends MainController
{
    /**
     *
     * @inheritdoc
     */
    public $modelClass = 'artsoft\models\Session';
    public $modelSearchClass = 'artsoft\logs\models\search\SessionSearch';
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
