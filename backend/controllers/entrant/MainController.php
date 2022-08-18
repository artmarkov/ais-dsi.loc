<?php

namespace backend\controllers\entrant;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Вступительные экзамены',  'url' => ['/entrant/default/index']],
    ];
}