<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\schoolplan\SchoolplanPerform;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanPerformSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="schoolplan-perform-index">
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
                        'model' => SchoolplanPerform::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'schoolplan-perform-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'schoolplan-perform-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'schoolplan-perform-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                /*'bulkActionOptions' => [
                    'gridId' => 'schoolplan-perform-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],*/
                'columns' => [
                   /* ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],*/
                    [
                        'attribute' => 'id',
                        'value' => function ($model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'studyplan_subject_id',
                        'value' => function ($model) {
                            return RefBook::find('subject_memo_4')->getValue($model->studyplan_subject_id);
                        },
                    ],
                    [
                        'attribute' => 'lesson_mark_id',
                        'value' => function ($model) {
                            return $model->lessonMark->mark_label;
                        },
                    ],
                    [
                        'attribute' => 'winner_id',
                        'value' => function ($model) {
                            return $model->getWinnerValue($model->winner_id);
                        },
                    ],

                    'resume',
                    [
                        'attribute' => 'status_exe',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return SchoolplanPerform::getStatusExeValue($model->status_exe);
                        }
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status_exe',
                        'optionsArray' => \common\models\schoolplan\SchoolplanPerform::getStatusExeOptionsList(),
                        'options' => ['style' => 'width:100px'],
                    ],
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status_sign',
                        'optionsArray' => \common\models\schoolplan\SchoolplanPerform::getStatusSignOptionsList(),
                        'options' => ['style' => 'width:100px'],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                        'width' => '90px',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['/schoolplan/default/perform', 'id' => $model->schoolplan_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['/schoolplan/default/perform', 'id' => $model->schoolplan_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['/schoolplan/default/perform', 'id' => $model->schoolplan_id, 'objectId' => $model->id, 'mode' => 'delete'], [
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


