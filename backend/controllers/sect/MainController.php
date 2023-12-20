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
            ['label' => 'Распределение по группам', 'url' => ['/sect/default/distribution', 'id' => $id]],
            ['label' => 'Нагрузка', 'url' => ['/sect/default/load-items', 'id' => $id]],
            ['label' => 'Элементы расписания', 'url' => ['/sect/default/schedule-items', 'id' => $id]],
            ['label' => 'Расписание группы', 'url' => ['/sect/default/schedule', 'id' => $id]],
            ['label' => 'Тематические/репертуарные планы', 'url' => ['/sect/default/thematic-items', 'id' => $id]],
            ['label' => 'Расписание консультаций', 'url' => ['/sect/default/consult-items', 'id' => $id]],
            ['label' => 'Журнал успеваемости группы', 'url' => ['/sect/default/studyplan-progress', 'id' => $id]],
        ];
    }
}