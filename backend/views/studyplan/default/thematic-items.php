<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\StudyplanThematicViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Thematic plans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan-thematic-index">
    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => SubjectSect::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                        </div>
                    </div>
                    <?php
                    Pjax::begin([
                        'id' => 'studyplan-thematic-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'kartik\grid\SerialColumn'],
                            [
                                'attribute' => 'subject_cat_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('subject_category_name')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('subject_category_name')->getValue($model->subject_cat_id ?? null);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'format' => 'raw',
                                'group' => true,
                            ],
                            [
                                'attribute' => 'subject_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('subject_name')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('subject_name')->getValue($model->subject_id ?? null);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'format' => 'raw',
                                'group' => true,
                            ],
                            [
                                'attribute' => 'subject_type_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('subject_type_name')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('subject_type_name')->getValue($model->subject_type_id ?? null);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'format' => 'raw',
                                'group' => true,
                            ],
                            [
                                'attribute' => 'subject_vid_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('subject_vid_name')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('subject_vid_name_dev')->getValue($model->subject_vid_id);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'format' => 'raw',
                                'group' => true,
                                'subGroupOf' => 1
                            ],
                            [
                                'attribute' => 'thematic_category',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => \common\models\studyplan\StudyplanThematic::getCategoryList(),
                                'value' => function ($model) {
                                    return \common\models\studyplan\StudyplanThematic::getCategoryValue($model->thematic_category);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'format' => 'raw',
                                'group' => true,
                                'subGroupOf' => 1
                            ],
                            'period_in:date',
                            'period_out:date',
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                                'width' => '90px',
                                'template' => '{create} {update} {delete}',
                                'buttons' => [
                                    'create' => function ($key, $model) {
                                        if($model->subject_sect_studyplan_id == null) {
                                            return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                                                Url::to(['/studyplan/default/thematic-items', 'id' => $model->studyplan_id, 'studyplan_subject_id' => $model->studyplan_subject_id, 'mode' => 'create']), [
                                                    'title' => Yii::t('art', 'Create'),
                                                    'data-method' => 'post',
                                                    'data-pjax' => '0',
                                                    'disabled' => true
                                                ]
                                            );
                                        }
                                        else {
                                            return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                                                Url::to(['/studyplan/default/thematic-items', 'id' => $model->studyplan_id, 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id, 'mode' => 'create']), [
                                                    'title' => Yii::t('art', 'Create'),
                                                    'data-method' => 'post',
                                                    'data-pjax' => '0',
                                                    'disabled' => true
                                                ]
                                            );
                                        }

                                    },
                                    'update' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-edit" aria-hidden="true"></i>',
                                            Url::to(['/studyplan/default/thematic-items', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_thematic_id, 'mode' => 'update']), [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                                            Url::to(['/studyplan/default/thematic-items', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_thematic_id, 'mode' => 'delete']), [
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
                                    'create' => function ($model) {
                                        return true;
                                    },
                                    'delete' => function ($model) {
                                        return $model->studyplan_thematic_id;
                                    },
                                    'update' => function ($model) {
                                        return $model->studyplan_thematic_id;
                                    }
                                ],
                            ],
                        ],
                        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Дисциплина', 'options' => ['colspan' => 5, 'class' => 'text-center warning']],
                                    ['content' => 'Тематический(репертуарный) план', 'options' => ['colspan' => 4, 'class' => 'text-center danger']],
                                ],
                                'options' => ['class' => 'skip-export'] // remove this row from export
                            ]
                        ],
                        'export' => [
                            'fontAwesome' => true
                        ],
                        'exportConfig' => [
                            'html' => [],
                            'csv' => [],
                            'txt' => [],
                            'xls' => [],
                        ],
                        'toolbar' => [
                            [
                                'content' => Html::a('Очистить',
                                    Url::to(['/studyplan/default/thematic-items', 'id' => $id]), [
                                        'title' => 'Очистить',
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-default'
                                    ]
                                ),
                            ],
                            '{export}',
                            '{toggleData}'
                        ],
                        'pjax' => true,
                        'bordered' => true,
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => false,
                        'hover' => false,
                        'floatHeader' => false,
//    'floatHeaderOptions' => ['top' => $scrollingTop],
//                        'showPageSummary' => true,
                        //'layout' => '{items}',
                        'panel' => [
                            'type' => GridView::TYPE_DEFAULT
                        ],
                    ]);

                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

