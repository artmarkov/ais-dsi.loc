<?php

use artsoft\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\activities\search\ActivitiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статистика по плану работы';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'category_id',
        'label' => $model['attributes']['category_id'],
        'footer' => 'ИТОГО:',
    ],
    [
        'attribute' => 'count_plan',
        'label' => $model['attributes']['count_plan'],
        'footer' => $model['all_summ']['count_plan'],
        'headerOptions' => ['class' => "grid"],
        'format' => 'raw',
    ],
    [
        'attribute' => 'count_users',
        'label' => $model['attributes']['count_users'],
        'footer' => $model['all_summ']['count_users'],
        'headerOptions' => ['class' => "grid"],
        'format' => 'raw',
    ],
    [
        'attribute' => 'count_winners',
        'label' => $model['attributes']['count_winners'],
        'footer' => $model['all_summ']['count_winners'],
        'headerOptions' => ['class' => "grid"],
        'format' => 'raw',
    ],
    [
        'attribute' => 'count_visitors',
        'label' => $model['attributes']['count_visitors'],
        'footer' => $model['all_summ']['count_visitors'],
        'headerOptions' => ['class' => "grid"],
        'format' => 'raw',
    ],

];


?>
<div class="schoolplan-stat-index">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <?= $this->render('_search', compact('model_date')) ?>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => SubjectSect::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'studyplan-progress-stat-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'studyplan-progress-stat-grid-pjax',
            ])
            ?>
            <?php
            echo GridView::widget([
                'id' => 'studyplan-progress-stat-grid',
                'pjax' => false,
                'showPageSummary' => false,
                'showFooter' => \artsoft\Art::isBackend(),
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $model['data'],
                    'sort' => [
                        'attributes' => array_keys($model['attributes'])
                    ],
                    'pagination' => false,
                ]),
                'panel' => [
                    'heading' => false,
                    'type' => '',
                    // 'footer' => $hints,
                ],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Мероприятия', 'options' => ['colspan' => 3, 'rowspan' => 2, 'class' => 'text-center warning', 'style' => 'vertical-align: middle;']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ],
                    [
                        'columns' => [
                            ['content' => 'Из итогов мероприятия', 'options' => ['colspan' => 3, 'class' => 'text-center info']]
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
</div>
