<?php

namespace artsoft\logs\controllers;


/**
 * UserVisitLogController implements the CRUD actions for UserVisitLog model.
 */
class DefaultController extends MainController
{
    /**
     *
     * @inheritdoc
     */
    public $modelClass = 'artsoft\models\UserVisitLog';

    /**
     *
     * @inheritdoc
     */
    public $modelSearchClass = 'artsoft\logs\models\search\UserVisitLogSearch';

    /**
     *
     * @inheritdoc
     */
  
    public $disabledActions = ['update'];

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