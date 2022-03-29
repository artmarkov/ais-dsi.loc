<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use kartik\grid\GridView;
use common\models\subjectsect\SubjectScheduleStudyplanView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Schedule');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'value' => function ($model) {
            return $model->studyplan_subject_id != 0 ? RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id) : RefBook::find('sect_memo_2')->getValue($model->subject_sect_studyplan_id);
        },
        'group' => true,
    ],
    [
        'attribute' => 'subject_sect_studyplan_id',
        'width' => '310px',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => RefBook::find('sect_name_3')->getList(),
        'value' => function ($model, $key, $index, $widget) {
            return RefBook::find('sect_name_3')->getValue($model->subject_sect_studyplan_id) ?? 'Индивидуально';
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,  // enable grouping
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'week_time',
        'value' => function ($model) {
            return $model->week_time;
        },
        'group' => true,
        'subGroupOf' => 2,
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
        'subGroupOf' => 2
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
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'teachers_id',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => false /*RefBook::find('teachers_fio')->getList()*/,
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,  // enable grouping
        'subGroupOf' => 5
    ],
    [
        'attribute' => 'load_time',
        'value' => function ($model) {
            return $model->load_time . ' ' . $model->getItemLoadNotice();
        },
        'format' => 'raw',
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
];
?>
<div class="subject-schedule-index">
    <div class="panel">
        <div class="panel-body">

            <?php
            Pjax::begin([
                'id' => 'subject-schedule-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table-condensed'],
//                        'showPageSummary' => true,
                'pjax' => true,
                'hover' => true,
                'panel' => [
                    'heading' => 'Элементы расписания',
                    'type' => 'default',
                    'after' => '',
                ],
                'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Дисциплина', 'options' => ['colspan' => 5, 'class' => 'text-center warning']],
                            ['content' => 'Нагрузка', 'options' => ['colspan' => 3, 'class' => 'text-center info']],
                            ['content' => 'Расписание занятий', 'options' => ['colspan' => 3, 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
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
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

