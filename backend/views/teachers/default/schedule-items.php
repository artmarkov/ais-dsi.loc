<?php

use artsoft\helpers\RefBook;
use common\models\studyplan\Studyplan;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use kartik\grid\GridView;
use common\models\subjectsect\SubjectScheduleView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subjectsect\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Schedule');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-schedule-index">
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
                        'id' => 'subject-schedule-grid-pjax',
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
                                'attribute' => 'subject_sect_studyplan_id',
                                'width' => '310px',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('sect_name_1')->getList(),
                                'value' => function ($model, $key, $index, $widget) {
                                    return RefBook::find('sect_name_1')->getValue($model->subject_sect_studyplan_id);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
                            ],
                            [
                                'attribute' => 'direction_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => \common\models\guidejob\Direction::getDirectionList(),
                                'value' => function ($model, $key, $index, $widget) {
                                    return $model->direction ? $model->direction->name : null;
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],

                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
                            ],
//                            [
//                                'attribute' => 'teachers_id',
//                                'filterType' => GridView::FILTER_SELECT2,
//                                'filter' => RefBook::find('teachers_fio')->getList(),
//                                'value' => function ($model) {
//                                    return RefBook::find('teachers_fio')->getValue($model->teachers_id);
//                                },
//                                'filterWidgetOptions' => [
//                                    'pluginOptions' => ['allowClear' => true],
//                                ],
//                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
//                                'group' => true,  // enable grouping
//                                'subGroupOf' => 1
//                            ],
                            [
                                'attribute' => 'load_time',
                                'value' => function ($model) {
                                    return $model->load_time . ' ' . $model->getTeachersOverLoadNotice();
                                },
                                'format' => 'raw',
                                'group' => true,  // enable grouping
                                'subGroupOf' => 4
                            ],
                            [
                                'attribute' => 'scheduleDisplay',
                                'value' => function ($model) {
                                    return $model->getScheduleDisplay();
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'auditory_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('auditory_memo_1')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                            ],
//                            'description',
//                            [
//                                'class' => 'kartik\grid\ActionColumn',
////                                'dropdown' => $this->dropdown,
////                                'dropdownOptions' => ['class' => 'float-right'],
//                                'urlCreator' => function ($action, $model, $key, $index) {
//                                    return '#';
//                                },
////                                'viewOptions' => ['title' => '', 'data-toggle' => 'tooltip'],
////                                'updateOptions' => ['title' => '', 'data-toggle' => 'tooltip'],
////                                'deleteOptions' => ['title' => '', 'data-toggle' => 'tooltip'],
////                                'headerOptions' => ['class' => 'kartik-sheet-style'],
//                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                                'width' => '90px',
                                'template' => '{create} {update} {delete}',
                                'buttons' => [
                                    'create' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                                            Url::to(['/teachers/default/schedule-items', 'id' => $model->teachers_id, 'load_id' => $model->teachers_load_id, 'mode' => 'create']), [
                                                'title' => Yii::t('art', 'Create'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                                'disabled' => true
                                            ]
                                        );
                                    },
                                    'update' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-edit" aria-hidden="true"></i>',
                                            Url::to(['/teachers/default/schedule-items', 'id' => $model->teachers_id, 'objectId' => $model->subject_schedule_id, 'mode' => 'update']), [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                                            Url::to(['/teachers/default/schedule-items', 'id' => $model->teachers_id, 'objectId' => $model->subject_schedule_id, 'mode' => 'delete']), [
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
                                        return $model->getTeachersScheduleNeed();
                                    },
                                    'delete' => function ($model) {
                                        return $model->subject_schedule_id !== null;
                                    },
                                    'update' => function ($model) {
                                        return $model->subject_schedule_id !== null;
                                    }
                                ]
                            ],
                        ],
                        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Дисциплина', 'options' => ['colspan' => 6, 'class' => 'text-center warning']],
                                    ['content' => 'Нагрузка', 'options' => ['colspan' => 2, 'class' => 'text-center info']],
                                    ['content' => 'Расписание занятий', 'options' => ['colspan' => 3, 'class' => 'text-center danger']],
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
                                    Url::to(['/teachers/default/schedule-items', 'id' => $id]), [
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
