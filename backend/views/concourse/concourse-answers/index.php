<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\concourse\Concourse;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\concourse\search\ConcourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//echo '<pre>' . print_r($data, true) . '</pre>'; die();
$columns = [];
$columns[] = ['class' => 'yii\grid\SerialColumn'];

/*$columns[] = [
    'attribute' => 'name',
    'label' => $data['attributes']['name'],
    'class' => 'artsoft\grid\columns\TitleActionColumn',
    'controller' => '/efficiency/default',
    'title' => function ($data) {
        return \artsoft\helpers\Html::a($data['name'],
            ['efficiency/default/details', 'id' => $data['id']], [
                'data-method' => 'post',
                'data-pjax' => '0',
            ]);
    },
    'buttonsTemplate' => '{details} {bar}',
    'buttons' => [
        'details' => function ($url, $data, $key) {
            return Html::a('Показатели',
                ['efficiency/default/details', 'id' => $data['id']], [
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
        },
        'bar' => function ($url, $data, $key) {
            return Html::a('График',
                ['efficiency/default/user-bar', 'id' => $data['id']], [
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
        }
    ],
    'format' => 'raw',
    'options' => ['style' => 'width:250px'],
    'headerOptions' => ['class' => "grid"],
];*/
$columns[] = [
    'attribute' => 'name',
    'label' => $data['attributes']['name'],
    'value' => function ($data) {
        return $data['name'];
    },
    'format' => 'raw',
    'options' => ['style' => 'width:250px'],
    'headerOptions' => ['class' => "grid"],
];
foreach ($data['root'] as $id => $name) {
    $columns[] = [
        'attribute' => $id,
        'label' => $name,
        'format' => 'raw',
        'headerOptions' => ['class' => "grid"]
    ];
}

$columns[] = [
    'attribute' => 'summ',
    'label' => $data['attributes']['summ'],
    'value' => function ($data) {
        return '<b>' . number_format($data['summ'], 2) . '</b>';
    },
    'footer' => 'ИТОГО:',
    'headerOptions' => ['class' => "grid"],
    'format' => 'raw',
];
$columns[] = [
    'attribute' => 'mid',
    'label' => $data['attributes']['mid'],
    'value' => function ($data) {
        return '<b>' . number_format($data['mid'], 2) . '</b>';
    },
    'footer' => number_format($data['all_mid'], 2),
    'headerOptions' => ['class' => "grid"],
    'format' => 'raw',
];
$columns[] = [
        'class' => 'kartik\grid\ActionColumn',
        'template' => '{update} {delete}',
        'buttons' => [
            'update' => function ($url, $data, $key){
                return \artsoft\helpers\Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['concourse/default/concourse-answers', 'id' => $data['modelId'], 'objectId' => $data['objectId'], 'userId' => $data['id'], 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($url, $data, $key){
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['concourse/default/concourse-answers', 'id' => $data['modelId'], 'objectId' => $data['objectId'], 'userId' => $data['id'], 'mode' => 'delete'], [
                        'title' => Yii::t('art', 'Delete'),
                        'aria-label' => Yii::t('art', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]);
            }
        ],
        'options' => ['style' => 'width:250px'],
        'headerOptions' => ['class' => 'kartik-sheet-style'],
];

?>

<div class="teachers-efficiency-summary">
    <div class="panel panel-default">
        <div class="panel-heading">
            Оценки конкурсной работы
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'id' => 'concourse-answers',
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $data['data'],
                    'sort' => [
                        'attributes' => array_keys($data['attributes'] + $data['root'])
                    ],
                    'pagination' => false,
                ]),
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Участники', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
                            ['content' => 'Критерии оценки', 'options' => ['colspan' => count($data['root']), 'class' => 'text-center info']],
                            ['content' => 'Итоги', 'options' => ['colspan' => 3, 'class' => 'text-center success']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
                'toolbar' => [
                    '{toggleData}'
                ],
                'showFooter' => true,
            ]);
            ?>
        </div>
    </div>
</div>

