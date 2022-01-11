<?php

use artsoft\helpers\RefBook;
use common\models\subjectsect\SubjectSectScheduleView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use kartik\grid\GridView;
use common\models\subjectsect\SubjectSectSchedule;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subjectsect\search\SubjectSectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Sect Schedules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-sect-schedule-index">
    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">

                    <?php
                    Pjax::begin([
                        'id' => 'subject-sect-schedule-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'kartik\grid\SerialColumn'],
//                            [
//                                'attribute' => 'subject_sect_id',
//                                'width' => '310px',
//                                'value' => function ($model, $key, $index, $widget) {
//                                    return $model->subject_sect_id;
//                                },
//
//                                'group' => true,  // enable grouping
//                            ],

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
                            ],
                            [
                                'attribute' => 'studyplan_subject_list',
                                'width' => '310px',
                                'filter' => RefBook::find('students_fio')->getList(),
                                'filterType' => GridView::FILTER_SELECT2,
                                'value' => function ($model, $key, $index, $widget) {
                                    $data = [];
                                    if (!empty($model->studyplan_subject_list)) {
                                        foreach (explode(',', $model->studyplan_subject_list) as $item => $studyplan_subject_id) {
                                            $student_id = RefBook::find('studyplan_subject-student')->getValue($studyplan_subject_id);
                                            $data[] = RefBook::find('students_fio')->getValue($student_id);
                                        }
                                    }
                                    return implode(',', $data);
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
                                    return $model->direction->name;
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],

                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
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
                                'subGroupOf' => 2
                            ],
                            [
                                'attribute' => 'week_time',
                                'value' => function ($model) {
                                    return $model->week_time;
                                },
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
//                            'plan_year',
//                            'subject_sect_schedule_id',
//                            [
//                                'attribute' => 'week_num',
//                                'filterType' => GridView::FILTER_SELECT2,
//                                'filter' => \artsoft\helpers\ArtHelper::getWeekList(),
//                                'value' => function ($model) {
//                                    return $model->week_num != 0 ? \artsoft\helpers\ArtHelper::getWeekList()[$model->week_num] : null;
//                                },
//                                'filterWidgetOptions' => [
//                                    'pluginOptions' => ['allowClear' => true],
//                                ],
//                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
//                            ],
//                            [
//                                'attribute' => 'week_day',
//                                'filterType' => GridView::FILTER_SELECT2,
//                                'filter' => \artsoft\helpers\ArtHelper::getWeekdayList(),
//                                'value' => function ($model) {
//                                    return isset($model->week_day) ? \artsoft\helpers\ArtHelper::getWeekdayList()[$model->week_day] : null;
//                                },
//                                'filterWidgetOptions' => [
//                                    'pluginOptions' => ['allowClear' => true],
//                                ],
//                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
//                            ],
//
//                            'time_in:time',
//                            'time_out:time',
                            [
                                'attribute' => 'auditory_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('auditory_memo_1')->getList(),
                                'options' => ['style' => 'width:300px'],
                                'value' => function ($model) {
                                    return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                            ],
                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                                'width' => '90px',
                                'template' => '{create} {update} {delete}',
                                'buttons' => [
                                    'create' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                                            Url::to(['/sect/default/schedule-items', 'id' => $model->subject_sect_id, 'load_id' => $model->teachers_load_id, 'mode' => 'create']), [
                                                'title' => Yii::t('art', 'Create'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                                'disabled' => true
                                            ]
                                        );
                                    },
                                    'update' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-edit" aria-hidden="true"></i>',
                                            Url::to(['/sect/default/schedule-items', 'id' => $model->subject_sect_id, 'objectId' => $model->subject_sect_schedule_id, 'mode' => 'update']), [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($key, $model) {
                                        return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                                            Url::to(['/sect/default/schedule-items', 'id' => $model->subject_sect_id, 'objectId' => $model->subject_sect_schedule_id, 'mode' => 'delete']), [
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
                                        return $model->subject_sect_schedule_id === null;
                                    },
                                    'delete' => function ($model) {
                                        return $model->subject_sect_schedule_id !== null;
                                    },
                                    'update' => function ($model) {
                                        return $model->subject_sect_schedule_id !== null;
                                    }
                                ]
                            ],
                        ],
                        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
//    'beforeHeader'=>[
//        [
//            'columns'=>[
//                ['content'=>'Header Before 1', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 2', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 3', 'options'=>['colspan'=>3, 'class'=>'text-center warning']],
//            ],
//            'options'=>['class'=>'skip-export'] // remove this row from export
//        ]
//    ],
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
                            ['content' => Html::a('Сбросить', ['/schedule'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('art', 'Reset')])
                            ],
                            '{export}',
                            // '{toggleData}'
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


