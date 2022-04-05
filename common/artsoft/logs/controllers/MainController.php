<?php

namespace artsoft\logs\controllers;

use artsoft\controllers\admin\BaseController;

class MainController extends BaseController
{
    public $tabMenu = [
        ['url' => ['/logs/default/index'], 'label' => 'Лог Посещения', 'visible' => 1],
        ['url' => ['/logs/session/index'], 'label' => 'Сеансы'],
        ['url' => ['/logs/request/index'], 'label' => 'Запросы'],
        ['url' => ['/logs/sigur/index'], 'label' => 'Лог СКУД'],
    ];

}