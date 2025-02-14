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

$columns[] = [
    'attribute' => 'scale',
    'label' => $data['attributes']['scale'],
    'value' => function ($data) {
        return $data['scale'];
    },
    //'footer' => round($data['all_mid'], 2),
    'headerOptions' => ['class' => "grid"],
    'format' => 'raw',
];

?>

<div class="concourse-stat">
    <div class="panel panel-default">
        <div class="panel-heading">
            Статистика конкурса
        </div>
        <div class="panel-body">
            <div class="col-sm-12">
                <?=  \yii\bootstrap\Alert::widget([
                    'body' => '<i class="fa fa-info-circle"></i> В статистике отражена активность пользователей по оценке конкурсных работ.',
                    'options' => ['class' => 'alert-info'],
                ]);
                ?>
            </div>
            <?= GridView::widget([
                'id' => 'concourse-answers',
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $data['data'],
                    'sort' => [
                        'attributes' => array_keys($data['attributes'])
                    ],
                    'pagination' => false,
                ]),
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Участники', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
                            ['content' => 'Шкала оценки конкурсных работ', 'options' => ['colspan' => 1, 'class' => 'text-center info']],
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


