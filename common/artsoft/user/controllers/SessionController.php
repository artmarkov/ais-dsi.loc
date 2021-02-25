<?php

namespace artsoft\user\controllers;

use artsoft\controllers\admin\BaseController;
use artsoft\models\Session;
use yii\data\ArrayDataProvider;

/**
 * SessionController implements the CRUD actions for Session model.
 */
class SessionController extends BaseController
{
    /**
     *
     * @inheritdoc
     */
    public $modelClass = 'artsoft\models\Session';
    public $modelSearchClass = 'artsoft\user\models\search\SessionSearch';
    public $disabledActions = ['view', 'create', 'update'];

    protected function getListMenu()
    {
        return [
            ['url' => ['/user/visit-log/index'], 'label' => 'Visit Log', 'visible' => 1],
            ['url' => ['/user/session/index'], 'label' => 'Session'],
            ['url' => ['/user/request/index'], 'label' => 'Request'],
        ];
    }
}
