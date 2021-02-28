<?php

namespace artsoft\user\controllers;

use artsoft\controllers\admin\BaseController;

class MainController extends BaseController
{
    public $tabMenu = [
        ['url' => ['/user/visit-log/index'], 'label' => 'Visit Log', 'visible' => 1],
        ['url' => ['/user/session/index'], 'label' => 'Session'],
        ['url' => ['/user/request/index'], 'label' => 'Request'],
    ];

}