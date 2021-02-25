<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use artsoft\queue\models\QueueSchedule;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel artsoft\queue\models\search\QueueScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/queue', 'Queue Schedules');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="queue-schedule-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= Html::encode($this->title) ?></h3>
            <?= Html::a(Yii::t('art', 'Add New'), ['/queue-schedule/default/create'], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    echo GridQuickLinks::widget([
                        'model' => QueueSchedule::className(),
                        'searchModel' => $searchModel,
                        'labels' => [
                            'all' => Yii::t('art', 'All'),
                            'active' => Yii::t('art/queue', 'Enable'),
                            'inactive' => Yii::t('art/queue', 'Disable'),
                        ]
                    ])
                    ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'queue-schedule-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'queue-schedule-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'queue-schedule-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'queue-schedule-grid',
                    'actions' => [
                        Url::to(['bulk-run']) => Yii::t('art/queue', 'Run now'),
                        Url::to(['bulk-activate']) => Yii::t('art/queue', 'Switch on'),
                        Url::to(['bulk-deactivate']) => Yii::t('art/queue', 'Switch off'),
                        Url::to(['bulk-delete']) => Yii::t('yii', 'Delete'),
                    ]
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],                    
                    [
                        'attribute' => 'title',
                        'class' => 'artsoft\grid\columns\TitleActionColumn',
                        'controller' => '/queue-schedule/default',
                        'options' => ['style' => 'width:350px'],
                        'title' => function(QueueSchedule $model) {
                            return Html::a($model->title, ['/queue-schedule/default/view', 'id' => $model->id], ['data-pjax' => 0]);
                        },
                        'buttonsTemplate' => '{update} {view} {delete} {switch_on} {switch_off} {run}',
                        'buttons' => [
                            'run' => function ($url, $model, $key) {
                                return Html::a(Yii::t('art/queue', 'Run now'), ['/queue-schedule/default/run', 'id' => $model->id], [
                                            'title' => Yii::t('art/queue', 'Run now'),
                                            'data-pjax' => '0'
                                                ]
                                );
                            },
                            'switch_on' => function ($url, $model, $key) {
                                return Html::a(Yii::t('art/queue', 'Switch on'), ['/queue-schedule/default/activate', 'id' => $model->id], [
                                            'title' => Yii::t('art/queue', 'Switch On'),
                                            'data-pjax' => '0'
                                                ]
                                );
                            },
                            'switch_off' => function ($url, $model, $key) {
                                return Html::a(Yii::t('art/queue', 'Switch off'), ['/queue-schedule/default/deactivate', 'id' => $model->id], [
                                            'title' => Yii::t('art/queue', 'Switch Off'),
                                            'data-pjax' => '0'
                                                ]
                                );
                            }
                        ],
                    ],
                    [
                        'attribute' => 'cron_expression',                        
                        'options' => ['style' => 'width:150px'],
                    ],                   
//                    'class',
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => QueueSchedule::getStatusOptionsList(),
                        'options' => ['style' => 'width:100px'],
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'priority',
                        'optionsArray' => QueueSchedule::getPriorityOptionsList(),
                        'options' => ['style' => 'width:100px'],
                    ],
                    [
                        'class' => 'artsoft\grid\columns\DateFilterColumn',
                        'attribute' => 'next_date',
                        'value' => function (QueueSchedule $model) {
                            return '<span style="font-size:85%;" class="label label-default">'
                            . $model->nextDatetime . '</span>';
                        },
                        'format' => 'raw',
                        'options' => ['style' => 'width:150px'],
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>