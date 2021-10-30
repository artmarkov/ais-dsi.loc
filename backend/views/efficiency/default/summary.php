<?php

use artsoft\grid\GridView;
use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $data */
/* @var $model_date */

$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Efficiencies'), 'url' => ['efficiency/default/index']];
$this->params['breadcrumbs'][] = 'Сводная таблица';
//echo '<pre>' . print_r($model_date, true) . '</pre>';
$columns = [];
$columns[] = ['class' => 'yii\grid\SerialColumn'];

$columns[] = [
    'attribute' => 'name',
    'label' => $data['attributes']['name'],
    'class' => 'artsoft\grid\columns\TitleActionColumn',
    'controller' => '/efficiency/default',
    'title' => function ($data) {
        return Html::a($data['name'],
            Url::to(['efficiency/default/details', 'id' => $data['id'], 'timestamp_in' => $data['date_in'], 'timestamp_out' => $data['date_out']]), [
                'data-method' => 'post',
                'data-pjax' => '0',
            ]);
    },
    'buttonsTemplate' => '{details} {bar}',
    'buttons' => [
        'details' => function ($url, $data, $key) {
            return Html::a('Показатели',
                Url::to(['efficiency/default/details', 'id' => $data['id'], 'timestamp_in' => $data['date_in'], 'timestamp_out' => $data['date_out']]), [
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
        },
        'bar' => function ($url, $data, $key) {
            return Html::a('График',
                Url::to(['efficiency/default/user-bar', 'id' => $data['id'], 'timestamp_in' => $data['date_in'], 'timestamp_out' => $data['date_out']]), [
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
        }
    ],
    'format' => 'raw',
    'options' => ['style' => 'width:250px'],
    'headerOptions' => ['class' => "grid"],
];

foreach ($data['root'] as $id => $name) {
    $columns[] = [
        'attribute' => $id,
        'label' => $name,
        'headerOptions' => ['class' => "grid"]
    ];
}
$columns[] = [
    'attribute' => 'stake',
    'label' => $data['attributes']['stake'],
    'headerOptions' => ['class' => "grid"]
];
$columns[] = [
    'attribute' => 'total',
    'label' => $data['attributes']['total'],
    'footer' => 'Итого:',
    'headerOptions' => ['class' => "grid"]
];
$columns[] = [
    'attribute' => 'total_sum',
    'label' => $data['attributes']['total_sum'],
    'value' => function ($data) {
        return number_format($data['total_sum'], 2);
    },
    'footer' => number_format($data['all_summ'], 2),
    'headerOptions' => ['class' => "grid"]
];
?>

    <div class="teachers-efficiency-summary">
        <div class="panel">
            <div class="panel-heading">
                Сводная таблица
            </div>
            <div class="panel-body">
                <?= $this->render('_search', compact('model_date')) ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Результаты запроса
                    </div>
                    <div class="panel-body">
                        <?= GridView::widget([
                            'id' => 'teachers-efficiency-summary',
                            'dataProvider' => new \yii\data\ArrayDataProvider([
                                'allModels' => $data['data'],
                                'sort' => [
                                    'attributes' => array_keys($data['attributes'] + $data['root'])
                                ],
                                'pagination' => false,
                            ]),
                            'columns' => $columns,
                            'showFooter' => true,
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$css = <<<CSS
.teachers-efficiency-summary .grid {
    white-space: normal; 
    vertical-align: top;
}
 .grid-view tbody tr td {
     height: 30px; 
}

CSS;

$this->registerCss($css);
?>