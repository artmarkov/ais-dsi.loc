<?php

namespace backend\controllers\venue;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Места проведения',  'url' => ['/venue/default/index']],
        ['label' => 'Страны',  'url' => ['/venue/country/index']],
        ['label' => 'Города',  'url' => ['/venue/sity/index']],
        ['label' => 'Округа',  'url' => ['/venue/district/index']],
    ];

}