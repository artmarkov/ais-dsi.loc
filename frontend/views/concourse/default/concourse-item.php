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
                    if ($modelsAnswers->getConcourseItemFullnessForUser($model->id)) {
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
                        'filter' => $users_list,
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
                        'options' => ['style' => 'width:350px'],
                        'format' => 'raw',
                    ],
                    'description:ntext',
                    [
                        'label' => 'Статус',
                        'value' => function (ConcourseItem $model) use ($modelsAnswers) {
                            return $modelsAnswers->getConcourseItemFullnessForUser($model->id) ? '<i class="fa fa-thumbs-up text-success" style="font-size: 1.5em;"></i> Заполнено'
                                : '<i class="fa fa-thumbs-down text-danger" style="font-size: 1.5em;"></i> В работе';
                        },
                        'contentOptions' => ['style' => 'text-align:center; vertical-align: middle;'],
                        'format' => 'raw',
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) use ($modelsAnswers) {
                                return Html::a('<span class="glyphicon glyphicon-certificate" aria-hidden="true"></span> Оценить работу',
                                    ['/concourse/default/concourse-item', 'id' => $model->concourse_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                        'title' => 'Оценить работу',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-xs btn-primary',
                                        'visible' => true,
                                        'disabled' => $modelsAnswers->getConcourseItemFullnessForUser($model->id)
                                    ]
                                );
                            },

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


