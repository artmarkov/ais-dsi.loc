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

}
