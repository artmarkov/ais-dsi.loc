<?php

namespace artsoft\user\controllers;

use artsoft\controllers\admin\BaseController;

class MainController extends BaseController
{
    public $tabMenu = [
        ['url' => ['/user/default/index'], 'label' => 'Пользователи'],
        ['url' => ['/user/permission/index'], 'label' => 'Права доступа'],
        ['url' => ['/user/permission-groups/index'], 'label' => 'Группы прав доступа'],
        ['url' => ['/user/role/index'], 'label' => 'Роли'],
    ];

}