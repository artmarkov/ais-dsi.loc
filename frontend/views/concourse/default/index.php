<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\concourse\Concourse;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\concourse\search\ConcourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Конкурсы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="concourse-index">
    <div class="panel">
        <div class="panel-heading">
            Конкурсы
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => Concourse::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'concourse-grid-pjax']) ?>
                </div>
            </div>

            <?php
            /* Pjax::begin([
                 'id' => 'concourse-grid-pjax',
             ])*/
            ?>

            <?=
            GridView::widget([
                // 'id' => 'concourse-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => false,
                /* 'bulkActionOptions' => [
                     'gridId' => 'concourse-grid',
                     'actions' => [Url::to(['bulk-delete']) => Yii::t('art', 'Delete')] //Configure here you bulk actions
                 ],*/
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function (Concourse $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'name',
                        'value' => function ($model) {
                            return Html::a($model->name,
                                ['/concourse/default/concourse-item', 'id' => $model->id],
                                [
                                    'title' => 'Раскрыть информацию',
                                    'data-pjax' => '0',
                                    'visible' => true
                                ]);
                        },
                        'format' => 'raw'
                    ],

                    'timestamp_in:date',
                    'timestamp_out:date',
                    [
                        'attribute' => 'description',
                        'value' => function (Concourse $model) {
                            return $model->description;
                        },
                        'format' => 'html',
                    ],
                    /* [
                         'attribute' => 'author_id',
                         'filter' => artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                         'value' => function (Concourse $model) {
                             return $model->author->userCommon ? $model->author->userCommon->fullName : $model->author_id;
                         },
                         'format' => 'raw',
                     ],*/
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [Concourse::STATUS_ACTIVE, Yii::t('art', 'Active'), 'primary'],
                            [Concourse::STATUS_INACTIVE, Yii::t('art', 'Inactive'), 'info'],
                        ],
                        'options' => ['style' => 'width:150px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($key, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    Url::to(['/concourse/default/concourse-item', 'id' => $model->id]), [
                                        'title' => 'Раскрыть информацию',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                        'visible' => true
                                    ]
                                );
                            },
                        ],
                        /* 'visibleButtons' => [
                             'create' => function ($model) {
                                 return Yii::$app->user->isGuest;
                             }
                         ],*/
                    ],
                ],
            ]);
            ?>

            <?php /* Pjax::end()*/ ?>
        </div>
    </div>
</div>


