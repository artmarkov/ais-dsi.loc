<?php

namespace backend\controllers\invoices;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Счета за обучение',  'url' => ['/invoices/default/index']],
    ];
}