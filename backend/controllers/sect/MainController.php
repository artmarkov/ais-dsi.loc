<?php

namespace backend\controllers\sect;

class MainController extends \backend\controllers\DefaultController
{
    public $tabMenu = [
        ['label' => 'Учебные группы',  'url' => ['/sect/default/index']],
    ];

    /**
     * @param $id
     * @return array
     */
    public function getMenu($id)
    {
        return [
            ['label' => 'Карточка группы', 'url' => ['/sect/default/update', 'id' => $id]],
            ['label' => 'Расписание группы', 'url' => ['/sect/default/schedule', 'id' => $id]],
            ['label' => 'Элементы расписания', 'url' => ['/sect/default/schedule-items', 'id' => $id]],
        ];
    }
}