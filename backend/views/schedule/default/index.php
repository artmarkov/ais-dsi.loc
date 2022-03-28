<?php

use artsoft\helpers\RefBook;
use common\models\studyplan\Studyplan;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use kartik\grid\GridView;
use common\models\subjectsect\SubjectScheduleStudyplanView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subjectsect\search\SubjectSectScheduleSearch */
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
                            <?= GridPageSize::widget(['pjaxId' => 'subject-schedule-grid-pjax']) ?>
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

//                            [
//                                'attribute' => 'studyplan_subject_id',
//                                'value' => function ($model, $key, $index, $widget) {
//                                    return RefBook::find('subject_memo_2')->getValue($model->studyplan_subject_id ?? null) . '-' . $model->week_time;
//                                },
//                            ],
                            [
                                'attribute' => 'student_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('students_fullname')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('students_fullname')->getValue($model->student_id);
                                },
                                'format' => 'raw',
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                            ],
                            [
                                'attribute' => 'programm_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('education_programm_short_name')->getList(),
                                'value' => function ($model, $key, $index, $widget) {
                                    return RefBook::find('education_programm_short_name')->getValue($model->programm_id ?? null);
                                },
                                'format' => 'raw',
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
                            ],
                            [
                                'attribute' => 'speciality_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('education_speciality')->getList(),
                                'value' => function ($model, $key, $index, $widget) {
                                    return RefBook::find('education_speciality_short')->getValue($model->speciality_id ?? null);
                                },
                                'format' => 'raw',
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
                            ],
                            [
                                'attribute' => 'plan_year',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => \artsoft\helpers\ArtHelper::getStudyYearsList(),
                                'value' => function ($model) {
                                    return \artsoft\helpers\ArtHelper::getStudyYearsList()[$model->plan_year];
                                },
                                'format' => 'raw',
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
                            ],
                            [
                                'attribute' => 'status',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => SubjectScheduleStudyplanView::getStatusList(),
                                'value' => function ($model) {
                                    return SubjectScheduleStudyplanView::getStatusValue($model->status);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
                            ],
                            [
                                'attribute' => 'course',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => \artsoft\helpers\ArtHelper::getCourseList(),
                                'value' => function ($model) {
                                    return \artsoft\helpers\ArtHelper::getCourseList()[$model->course];
                                },
                                'format' => 'raw',
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
                            ],

                            [
                                'attribute' => 'subject_cat_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('subject_category_name')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('subject_category_name')->getValue($model->subject_cat_id);
                                },
                                'format' => 'raw',
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
                            ],
                            [
                                'attribute' => 'subject_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('subject_name')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('subject_name')->getValue($model->subject_id);
                                },
                                'format' => 'raw',
                                'group' => true,  // enable grouping
                                'subGroupOf' => 7
                            ],
                            [
                                'attribute' => 'subject_type_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('subject_type_name')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('subject_type_name_dev')->getValue($model->subject_type_id);
                                },
                                'format' => 'raw',
                                'group' => true,  // enable grouping
                                'subGroupOf' => 7
                            ],
                            [
                                'attribute' => 'subject_vid_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('subject_vid_name')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('subject_vid_name_dev')->getValue($model->subject_vid_id);
                                },
                                'format' => 'raw',
                                'group' => true,  // enable grouping
                                'subGroupOf' => 7
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
                                'subGroupOf' => 7
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
                                'subGroupOf' => 7
                            ],
                            [
                                'attribute' => 'teachers_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('teachers_fio')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('teachers_fio')->getValue($model->teachers_id);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                                'subGroupOf' => 7
                            ],
                            [
                                'attribute' => 'teachers_load_week_time',
                                'value' => function ($model) {
                                    return $model->teachers_load_week_time . ' ' . $model->getTeachersOverLoadNotice();
                                },
                                'format' => 'raw',
                                'group' => true,  // enable grouping
                                'subGroupOf' => 13
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                                'width' => '90px',
                                'template' => '{create} {update} {delete}',
                                'buttons' => [
                                    'create' => function ($key, $model) {
                                        if($model->subject_sect_studyplan_id == null) {
                                            return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                                                Url::to(['/schedule/teachers-load/create', 'studyplan_subject_id' => $model->studyplan_subject_id]), [
                                                    'title' => Yii::t('art', 'Create'),
                                                    'data-method' => 'post',
                                                    'data-pjax' => '0',
                                                    'disabled' => true
                                                ]
                                            );
                                        }
                                        else {
                                            return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                                                Url::to(['/schedule/teachers-load/create', 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id]), [
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
                                            Url::to(['/schedule/teachers-load/update', 'id' => $model->teachers_load_id]), [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                                            Url::to(['/schedule/teachers-load/delete', 'id' => $model->teachers_load_id]), [
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
                                        return $model->getTeachersLoadsNeed();
                                    },
                                    'delete' => function ($model) {
                                        return $model->teachers_load_id !== null;
                                    },
                                    'update' => function ($model) {
                                        return $model->teachers_load_id !== null;
                                    }
                                ],
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
                                            Url::to(['/schedule/default/create', 'load_id' => $model->teachers_load_id,]), [
                                                'title' => Yii::t('art', 'Create'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                                'disabled' => true
                                            ]
                                        );
                                    },
                                    'update' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-edit" aria-hidden="true"></i>',
                                            Url::to(['/schedule/default/update', 'id' => $model->subject_schedule_id]), [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                                            Url::to(['/schedule/default/delete', 'id' => $model->subject_schedule_id]), [
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
                                    ['content' => 'Индивидуальный план', 'options' => ['colspan' => 7, 'class' => 'text-center success']],
                                    ['content' => 'Дисциплина', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                                    ['content' => 'Нагрузка', 'options' => ['colspan' => 5, 'class' => 'text-center info']],
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
                                    Url::to(['/schedule']), [
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
                        'showPageSummary' => false,
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

