<?php

use artsoft\grid\SortableGridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\question\QuestionAttribute;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\question\search\QuestionAttributeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/question', 'Questions Attributes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="question-attribute-index">
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
                        'model' => QuestionAttribute::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'question-attribute-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'question-attribute-grid-pjax',
            ])
            ?>

            <?=
            SortableGridView::widget([
                'id' => 'question-attribute-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => false,
                'sortableAction' => ['grid-sort'],
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function (QuestionAttribute $model) {
                            return sprintf('#%06d', $model->id);
                        },
                    ],
                    [
                        'attribute' => 'type_id',
                        'value' => function (QuestionAttribute $model) {
                            return $model::getTypeValue($model->type_id);
                        },
                    ],
                    'label',
                    'description',
                    'hint',
                    [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'required',
                        'optionsArray' => [
                            [1, 'Да', 'success'],
                            [0, 'Нет', 'danger'],
                        ],
                        'options' => ['style' => 'width:150px']
                    ],
                     [
                        'class' => 'artsoft\grid\columns\StatusColumn',
                        'attribute' => 'unique_flag',
                        'optionsArray' => [
                            [1, 'Да', 'success'],
                            [0, 'Нет', 'danger'],
                        ],
                        'options' => ['style' => 'width:150px']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                                    ['question/default/question-attribute', 'id' => $model->question_id, 'objectId' => $model->id, 'mode' => 'update'], [
                                        'title' => Yii::t('art', 'Edit'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                                    ['question/default/question-attribute', 'id' => $model->question_id, 'objectId' => $model->id, 'mode' => 'view'], [
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                                    ['question/default/question-attribute', 'id' => $model->question_id, 'objectId' => $model->id, 'mode' => 'delete'], [
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


