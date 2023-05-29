<?php

use artsoft\models\User;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\entrant\EntrantComm;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\entrant\search\EntrantCommSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Entrant Comms');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entrant-comm-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => EntrantComm::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'entrant-comm-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'entrant-comm-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'entrant-comm-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'entrant-comm-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'id',
                        'value' => function (EntrantComm $model) {
                            return sprintf('#%06d', $model->id);
                        },
                        'options' => ['style' => 'width:100px']
                    ],
                    [
                        'attribute' => 'name',
                        'value' => function (EntrantComm $model) {
                            return $model->name;
                        },
                    ],
                    [
                        'attribute' => 'division_id',
                        'value' => function (EntrantComm $model) {
                            return \artsoft\helpers\RefBook::find('division_name')->getValue($model->division_id);
                        }
                        ,
                        'label' => Yii::t('art/guide', 'Name Division'),
                        'filter' => \artsoft\helpers\RefBook::find('division_name')->getList()
                    ],
                    [
                        'attribute' => 'department_list',
                        'filter' => \artsoft\helpers\RefBook::find('department_name')->getList(),
                        'value' => function (EntrantComm $model) {
                            $v = [];
                            foreach ($model->department_list as $id) {
                                if (!$id) {
                                    continue;
                                }
                                $v[] = \artsoft\helpers\RefBook::find('department_name_dev')->getValue($id) ?? '';
                            }
                            return implode(', ', $v);
                        },
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'plan_year',
                        'filter' => \artsoft\helpers\ArtHelper::getStudyYearsList(),
                        'value' => function (EntrantComm $model) {
                            return \artsoft\helpers\ArtHelper::getStudyYearsList()[$model->plan_year];
                        },
                        'options' => ['style' => 'width:100px'],
                        'format' => 'raw',
                    ],
                    'timestamp_in:date',
                    'timestamp_out:date',
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return [$action, 'id' => $model->id];
                        },
                        'controller' => '/entrant/default',
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['/entrant/default/update', 'id' => $model->id], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ],

                                );
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['/entrant/default/view', 'id' => $model->id], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['/entrant/default/delete', 'id' => $model->id], [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                        ],
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


