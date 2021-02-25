<?php

namespace artsoft\user\controllers;

use artsoft\controllers\admin\BaseController;

/**
 * UserVisitLogController implements the CRUD actions for UserVisitLog model.
 */
class VisitLogController extends BaseController
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
    public $modelSearchClass = 'artsoft\user\models\search\UserVisitLogSearch';

    /**
     *
     * @inheritdoc
     */
  
    public $disabledActions = ['update'];

    protected function getListMenu()
    {
        return [
            ['url' => ['/user/visit-log/index'], 'label' => 'Visit Log', 'visible' => 1],
            ['url' => ['/user/session/index'], 'label' => 'Session'],
            ['url' => ['/user/request/index'], 'label' => 'Request'],
        ];
    }

}