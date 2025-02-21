<?php

use artsoft\grid\GridView;
use yii\widgets\Pjax;
use common\models\concourse\ConcourseItem;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\concourse\search\ConcourseItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelsAnswers */

$this->title = 'Конкурсные работы';
$this->params['breadcrumbs'][] = $this->title;

$users_list = artsoft\models\User::getUsersListByCategory(['teachers'], false);
?>
<div class="concourse-item-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="col-sm-12">
                <?=  \yii\bootstrap\Alert::widget([
                    'body' => '<i class="fa fa-info-circle"></i> Нулевые значения в средней оценке не учитываются.',
                    'options' => ['class' => 'alert-info'],
                ]);
                ?>
            </div>
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
                    <?= GridPageSize::widget(['pjaxId' => 'concourse-item-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'concourse-item-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'concourse-item-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function (ConcourseItem $model) use ($modelsAnswers) {
                    if ($modelsAnswers->getConcourseItemFullness($model->id) == 100) {
                        return ['class' => 'success'];
                    };
                    return [];

                },
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function (ConcourseItem $model) {
                            return sprintf('#%06d', $model->id);
                        },
                        'options' => ['style' => 'width:50px'],

                    ],
                    [
                        'attribute' => 'name',
                        'value' => function (ConcourseItem $model) {
                            return $model->name;
                        },
                        'options' => ['style' => 'width:450px'],
                    ],
                    [
                        'attribute' => 'authors_list',
                        'value' => function (ConcourseItem $model) use ($users_list) {
                            $v = [];
                            foreach ($model->authors_list as $id) {
                                if (!$id) {
                                    continue;
                                }
                                $v[] = $users_list[$id] ?? $id;
                            }
                            return implode(', ', $v);

                        },
                        'options' => ['style' => 'width:400px'],
                        'hidden' => true,
                        'hiddenFromExport'=> false,
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'authors_list',
                        'filter' => $users_list,
                        'value' => function (ConcourseItem $model) use ($users_list) {
                            $v = [];
                            foreach ($model->authors_list as $id) {
                                if (!$id) {
                                    continue;
                                }
                                $v[] = $users_list[$id] ?? $id;
                            }
                            return implode(', </br>', $v);

                        },
                        'options' => ['style' => 'width:400px'],
                        'hidden' => false,
                        'hiddenFromExport'=> true,
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'description',
                        'value' => function (ConcourseItem $model)  {

                            return $model->description;

                        },
                        'hiddenFromExport'=> true,
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Заполненность %',
                        'value' => function (ConcourseItem $model) use ($modelsAnswers) {

                            return round($modelsAnswers->getConcourseItemFullness($model->id), 2) . '%';

                        },
                        'options' => ['style' => 'width:150px'],
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Средний балл',
                        'value' => function (ConcourseItem $model) use ($modelsAnswers) {

                            return str_replace('.', ',', round($modelsAnswers->getMiddleMark($model->id), 2));

                        },
                        'options' => ['style' => 'width:150px'],
                        'format' => 'raw',
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['concourse/default/concourse-item', 'id' => $model->concourse_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['concourse/default/concourse-item', 'id' => $model->concourse_id, 'objectId' => $model->id, 'mode' => 'delete'], [
                                        'title' => Yii::t('art', 'Delete'),
                                        'aria-label' => Yii::t('art', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                            }
                        ],
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>


