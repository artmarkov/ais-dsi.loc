<?php

namespace backend\controllers\students;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Ученики школы',  'url' => ['/students/default/index']],
        ['label' => 'Предварительная запись',  'url' => ['/students/preregistration/index']],
    ];
}