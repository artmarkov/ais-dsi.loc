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

}