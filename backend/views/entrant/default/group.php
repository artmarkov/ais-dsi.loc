<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\entrant\EntrantGroup;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\entrant\search\EntrantGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="entrant-group-index">
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
                        'model' => EntrantGroup::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'entrant-group-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'entrant-group-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'entrant-group-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'entrant-group-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],
                'columns' => [
                    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'attribute' => 'name',
                        'value' => function (EntrantGroup $model) {
                            return $model->name;
                        },
                    ],
                    [
                        'attribute' => 'prep_flag',
                        'value' => function (EntrantGroup $model) {
                            return EntrantGroup::getPrepValue($model->prep_flag);
                        },

                        'filter' => EntrantGroup::getPrepList(),
                    ],
                    'timestamp_in:datetime',
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    Url::to(['/entrant/default/group', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'update']), [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    Url::to(['/entrant/default/group', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'view']), [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    Url::to(['/entrant/default/group', 'id' => $model->comm_id, 'objectId' => $model->id, 'mode' => 'delete']), [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                        ],
//                        'visibleButtons' => [
//                            'create' => function ($model) {
//                                return true;
//                            },
//                            'delete' => function ($model) {
//                                return true;
//                            },
//                            'update' => function ($model) {
//                                return true;
//                            }
//                        ]
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


