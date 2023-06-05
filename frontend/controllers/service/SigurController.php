<?php

namespace frontend\controllers\service;

/**
 * SigurController implements the CRUD actions for common\models\service\UsersCardLog model.
 */
class SigurController extends \frontend\controllers\DefaultController
{
    public $modelClass       = 'common\models\service\UsersCardLog';
    public $modelSearchClass = 'common\models\service\search\UsersCardLogSearch';

    public $disabledActions = ['create', 'update'];

    public function init()
    {
        $this->viewPath = '@backend/views/service/sigur';
        parent::init();
    }

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