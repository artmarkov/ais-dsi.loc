<?php

/* @var $this yii\web\View */

$this->title = 'Статистика по посещаемости согласно расписанию';
$this->params['breadcrumbs'][] = $this->title;

for ($i = 0; $i < 7; $i++) {
    echo \backend\widgets\dashboard\Visits::widget(['timestamp' => time() + (86400 * $i)]);
}
