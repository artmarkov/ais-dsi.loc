<?php

use artsoft\helpers\RefBook;
use common\models\teachers\Teachers;use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;
use common\models\schedule\SubjectScheduleView;
use artsoft\helpers\NoticeDisplay;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */
/* @var $model_confirm */
/* @var $noteModel */

$this->title = Yii::t('art/guide', 'Subject Schedule');
$this->params['breadcrumbs'][] = $this->title;

$teachers_list = RefBook::find('teachers_fio')->getList();
$auditory_list = RefBook::find('auditory_memo_1')->getList();
$noteModel = NoticeDisplay::getData($dataProvider->models, $model_date->plan_year);
$readonly = ($noteModel->confirmIsAvailable() && Teachers::isOwnTeacher($model->id)) ? false : true;
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
        'filter' => false,
        'width' => '310px',
        'value' => function ($model) {
            $value = $model->sect_name . $model->getSectNotice();

            if (\artsoft\Art::isBackend()) {
                if ($model->subject_sect_id == 0) {
                    return Html::a($value,
                        ['/studyplan/default/load-items', 'id' => $model->studyplan_id],
                        [
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                } else {
                    return Html::a($value,
                        ['/sect/default/studyplan-progress', 'id' => $model->subject_sect_id, 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id],
                        [
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                }
            } else {
                return $value;
            }
        },

        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
        'label' => Yii::t('art/guide', 'Sect') . '/' . Yii::t('art/student', 'Student'),
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
        'subGroupOf' => 5
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
        'visible' => \artsoft\Art::isFrontend() && Teachers::isOwnTeacher($model->id) && in_array($model_confirm->confirm_status, [0,3]),
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
            'create' => function (SubjectScheduleView $model) use($noteModel,$model_confirm) {
                return $noteModel->getTeachersScheduleNeed($model) && $model->subject_sect_id == 0 ;
            },
              'delete' => function ($model) use($model_confirm) {
                return $model->subject_schedule_id !== null && $model->subject_sect_id == 0;
            },
            'update' => function ($model) use($model_confirm) {
                return $model->subject_schedule_id !== null && $model->subject_sect_id == 0;
            }

        ]
    ],
];
?>
<div class="subject-schedule-index">
    <div class="panel">
        <div class="panel-heading">
            Элементы расписания: <?php echo RefBook::find('teachers_fullname')->getValue($model->id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <?= \artsoft\models\User::hasRole('reestrFrontend', false) ? '' : $this->render('_confirm', compact('model_confirm', 'readonly')) ?>
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
                    <?php if (\artsoft\Art::isBackend()): ?>
                        <?= Html::a('<i class="fa fa-calendar" aria-hidden="true"></i> Отчет по расписанию',
                            ['/reports/default/teachers-schedule', 'id' => $model->id],
                            [
                                'target' => '_blank',
                                'class' => 'btn btn-warning',
                            ]); ?>
                    <?php endif;?>
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

