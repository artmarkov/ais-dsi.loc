<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */

if ($dataProvider) {
    echo ListView::widget([
        'showOnEmpty' => true,
        'dataProvider' => $dataProvider,
        'layout' => "{items}",
        'options' => [
            'tag' => 'ul',
            'class' => 'sortable',
        ],
        'itemOptions' => [
            'tag' => 'li',
            'class' => 'sortable-item',
        ],
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('attribute', ['model' => $model]);
        },
    ]);
}