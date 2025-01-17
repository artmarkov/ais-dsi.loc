<?php

use artsoft\grid\SortableGridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\concourse\ConcourseCriteria;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\concourse\search\ConcourseCriteriaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Критерии оценки конкурса';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="concourse-criteria-index">
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
                        'model' => ConcourseCriteria::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'concourse-criteria-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'concourse-criteria-grid-pjax',
            ])
            ?>

            <?=
            SortableGridView::widget([
                'id' => 'concourse-criteria-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => false,
                'sortableAction' => ['grid-sort'],
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function (ConcourseCriteria $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    'name',
                    'name_dev',
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['concourse/default/concourse-criteria', 'id' => $model->concourse_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['concourse/default/concourse-criteria', 'id' => $model->concourse_id, 'objectId' => $model->id, 'mode' => 'delete'], [
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

                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


