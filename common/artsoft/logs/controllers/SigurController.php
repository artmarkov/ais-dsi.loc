<?php

namespace artsoft\logs\controllers;

/**
 * DefaultController implements the CRUD actions for common\models\sigur\UsersCardLog model.
 */
class SigurController extends MainController
{
    public $modelClass       = 'common\models\sigur\UsersCardLog';
    public $modelSearchClass = 'common\models\sigur\search\UsersCardLogSearch';

    public $disabledActions = ['create', 'update'];

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