<?php

use artsoft\grid\GridView;
use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $data */
/* @var $model_date */

$this->params['breadcrumbs'][] = ['label' => 'Журнал посещаемости', 'url' => ['/reports/working-time/index']];
$this->params['breadcrumbs'][] = 'Сводная таблица';
//echo '<pre>' . print_r($model_date, true) . '</pre>';

$columns = [
    [
        'attribute' => 'id',
        'value' => function ($data) {
            return sprintf('#%06d', $data['id']);
        },
        'label' => $data['attributes']['id'],
        'headerOptions' => ['class' => "grid"]
    ],
    [
        'attribute' => 'name',
        'label' => $data['attributes']['name'],
        'headerOptions' => ['class' => "grid"]
    ],
    [
        'attribute' => 'total_in',
        'value' => function ($data) {
            return $data['total_in'] . ' мин';
        },
        'label' => $data['attributes']['total_in'],
        'headerOptions' => ['class' => "grid"]
    ],
    [
        'attribute' => 'total_out',
        'value' => function ($data) {
            return $data['total_out'] . ' мин';
        },
        'label' => $data['attributes']['total_out'],
        'headerOptions' => ['class' => "grid"]
    ],
    [
        'attribute' => 'total_truancy',
        'label' => $data['attributes']['total_truancy'],
        'headerOptions' => ['class' => "grid"]
    ],
    [
        'attribute' => 'total_reserv',
        'label' => $data['attributes']['total_reserv'],
        'headerOptions' => ['class' => "grid"]
    ],
    [
        'attribute' => 'total_exit',
        'label' => $data['attributes']['total_exit'],
        'headerOptions' => ['class' => "grid"]
    ]
];

?>
    <div class="teachers-efficiency-summary">
        <div class="panel">
            <div class="panel-heading">
                Сводная таблица
            </div>
            <div class="panel-body">
                <?= $this->render('_search_stat', compact('model_date')) ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Результаты запроса
                    </div>
                    <div class="panel-body">
                        <?= GridView::widget([
                            'id' => 'working-time-summary',
                            'dataProvider' => new \yii\data\ArrayDataProvider([
                                'allModels' => $data['data'],
                                'sort' => [
                                    'attributes' => array_keys($data['attributes'])
                                ],
                                'pagination' => false,
                            ]),
                            'columns' => $columns,
                            'toolbar' => false,
                            'showFooter' => false,
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$css = <<<CSS
.working-time-summary .grid {
    white-space: normal; 
    vertical-align: top;
}
 .grid-view tbody tr td {
     height: 30px; 
}

CSS;

$this->registerCss($css);
?>