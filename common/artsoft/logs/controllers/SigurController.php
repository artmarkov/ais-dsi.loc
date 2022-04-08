<?php

namespace artsoft\logs\controllers;

/**
 * SigurController implements the CRUD actions for common\models\service\UsersCardLog model.
 */
class SigurController extends MainController
{
    public $modelClass       = 'common\models\service\UsersCardLog';
    public $modelSearchClass = 'common\models\service\search\UsersCardLogSearch';

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