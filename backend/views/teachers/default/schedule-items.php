<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;
use common\models\schedule\SubjectScheduleView;
use artsoft\helpers\NoticeDisplay;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */

$this->title = Yii::t('art/guide', 'Subject Schedule');
$this->params['breadcrumbs'][] = $this->title;

$teachers_list = RefBook::find('teachers_fio')->getList();
$auditory_list = RefBook::find('auditory_memo_1')->getList();
$noteModel = NoticeDisplay::getData($dataProvider->models, $model_date->plan_year);

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'subject',
        'value' => function ($model) {
            return $model->subject;
        },
        'group' => true,
    ],
    [
        'attribute' => 'sect_name',
        'width' => '310px',
        'value' => function ($model) {
            return $model->sect_name ? (\artsoft\Art::isBackend() && $model->studyplan_id != 0 ? Html::a($model->sect_name . $model->getSectNotice(),
                ['/studyplan/default/load-items', 'id' => $model->studyplan_id],
                [
                    'target' => '_blank',
                    'data-pjax' => '0',
//                    'class' => 'btn btn-info',
                ])  : $model->sect_name . $model->getSectNotice()) : null;
        },
        'label' =>  Yii::t('art/guide', 'Sect').'/'.Yii::t('art/student', 'Student'),
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
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
        'attribute' => 'direction_id',
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 3
    ],
    [
        'attribute' => 'teachers_id',
        'value' => function ($model) use ($teachers_list){
            return $teachers_list[$model->teachers_id] ?? '';
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'load_time',
        'value' => function ($model) {
            return $model->load_time /*. ' ' . $model->getItemLoadNotice()*/;
        },
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'scheduleDisplay',
        'width' => '300px',
        'value' => function (SubjectScheduleView $model) use($noteModel) {
            return $model->getScheduleDisplay() . $noteModel->getScheduleNotice($model);
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'auditory_id',
        'width' => '300px',
        'value' => function ($model) use ($auditory_list){
            return $auditory_list[$model->auditory_id] ?? '';
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'visible' => \artsoft\Art::isBackend(),
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    ['/teachers/default/schedule-items', 'id' => $model->teachers_id, 'load_id' => $model->teachers_load_id, 'mode' => 'create'], [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/teachers/default/schedule-items', 'id' => $model->teachers_id, 'objectId' => $model->subject_schedule_id, 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/teachers/default/schedule-items', 'id' => $model->teachers_id, 'objectId' => $model->subject_schedule_id, 'mode' => 'delete'], [
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
            'create' => function (SubjectScheduleView $model) use($noteModel) {
                return $noteModel->getTeachersScheduleNeed($model);
            },
            'delete' => function ($model) {
                return $model->subject_schedule_id !== null;
            },
            'update' => function ($model) {
                return $model->subject_schedule_id !== null;
            }
        ]
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'visible' => \artsoft\Art::isFrontend(),
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    ['/teachers/schedule-items/create', 'load_id' => $model->teachers_load_id], [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/teachers/schedule-items/update', 'id' => $model->subject_schedule_id], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/teachers/schedule-items/delete', 'id' => $model->subject_schedule_id], [
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
            'create' => function (SubjectScheduleView $model) use($noteModel) {
                return $noteModel->getTeachersScheduleNeed($model) && $model->subject_sect_id == 0;
            },
            'delete' => function ($model) {
                return $model->subject_schedule_id && $model->subject_sect_id == 0;
            },
            'update' => function ($model) {
                return $model->subject_schedule_id && $model->subject_sect_id == 0;
            }
        ]
    ],
];
?>
<div class="subject-schedule-index">
    <div class="panel">
        <div class="panel-heading">
            Элементы расписания: <?php echo RefBook::find('teachers_fio')->getValue($model->id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
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
                    <?php /*\artsoft\grid\GridPageSize::widget(['pjaxId' => 'subject-schedule-grid-pjax']) */?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'subject-schedule-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'subject-schedule-grid',
                'pjax' => false,
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                            ['content' => 'Нагрузка', 'options' => ['colspan' => 3, 'class' => 'text-center info']],
                            ['content' => 'Расписание занятий', 'options' => ['colspan' => 3, 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

