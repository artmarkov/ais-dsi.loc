<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\schoolplan\SchoolplanActivity;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelScoolplan \common\models\schoolplan\Schoolplan */

$this->title = Yii::t('art', 'Schoolplan Activities');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="schoolplan-activity-index">
    <div class="panel">
        <div class="panel-heading">
            <?= (\artsoft\Art::isFrontend() &&  $modelScoolplan->isAuthor()) || \artsoft\Art::isBackend() ? \artsoft\helpers\ButtonHelper::createButton() : ''; ?>
        </div>
        <div class="panel-body">
            <?php if ($modelScoolplan): ?>
                <div class="panel">
                    <div class="panel-heading">
                        Планировщик мероприятия
                    </div>
                    <div class="panel-body">
                        <?= \yii\widgets\DetailView::widget([
                            'model' => $modelScoolplan,
                            'attributes' => [
                                'title',
                                'datetime_in',
                                'datetime_out',
                            ],
                        ]) ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => SchoolplanActivity::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'schoolplan-activity-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'schoolplan-activity-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'schoolplan-activity-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
               /* 'bulkActionOptions' => [
                    'gridId' => 'schoolplan-activity-grid',
                    'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                ],*/
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function (SchoolplanActivity $model) {
                            return sprintf('#%06d', $model->id);
                        },
                        'options' => ['style' => 'width:10px']
                    ],
                    'datetime_in:datetime',
                    /*[
                        'attribute' => 'author_id',
                        'filter' => artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                        'value' => function (SchoolplanActivity $model) {
                            return $model->author->userCommon ? $model->author->userCommon->fullName : $model->author_id;
                        },
                        'format' => 'raw',
                    ],*/
                    [
                        'attribute' => 'executor_id',
                        'filter' => artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                        'value' => function (SchoolplanActivity $model) {
                            return $model->executor->userCommon ? $model->executor->userCommon->fullName : $model->executor_id;
                        },
                        'format' => 'raw',
                    ],
                    'name',
                    'places',
//                    'author_comment:ntext',
//                    'executor_comment:ntext',
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'activity_status',
                        'optionsArray' => \common\models\schoolplan\SchoolplanActivity::getStatusExeOptionsList(),
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
                                    ['/schoolplan/default/activity', 'id' => $model->schoolplan_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['/schoolplan/default/activity', 'id' => $model->schoolplan_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                        'title' => Yii::t('art', 'View'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['/schoolplan/default/activity', 'id' => $model->schoolplan_id, 'objectId' => $model->id, 'mode' => 'delete'], [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                        ],
                        'visibleButtons' => [
                            'view' => function ($model) {
                                return true;
                            },
                            'update' => function ($model) {
                                return \artsoft\Art::isBackend() ? true : $model->isAuthor();
                            },
                            'delete' => function ($model) {
                                return \artsoft\Art::isBackend() ? true : $model->isAuthor();
                            },
                        ]
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


