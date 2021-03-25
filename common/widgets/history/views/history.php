<?php

use yii\widgets\Pjax;
use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;

?>
<div class="history-index">
    <div class="row">
        <div class="col-sm-12 text-right">
            <?= GridPageSize::widget(['pjaxId' => 'history-grid-pjax']) ?>
        </div>
    </div>
    <?php
    Pjax::begin([
        'id' => 'history-grid-pjax',
    ])
    ?>
    <?= GridView::widget([
        'id' => 'history-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $filterModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width:20px'],],
            ['attribute' => 'type', 'label' => 'Событие'],
            ['attribute' => 'attr_label', 'label' => 'Параметр'],
            ['attribute' => 'display_value_old', 'label' => 'Старое'],
            ['attribute' => 'display_value_new', 'label' => 'Новое'],
            ['attribute' => 'updated_at', 'label' => 'Дата'],
            ['attribute' => 'updated_by_username', 'label' => 'Инициатор'],
        ],
    ]);
    ?>
    <?php Pjax::end() ?>
</div>
