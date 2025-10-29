<?php

use artsoft\helpers\RefBook;
use common\models\teachers\Teachers;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;
use common\models\subjectsect\SubjectScheduleStudyplanView;
use artsoft\helpers\NoticeConsultDisplay;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\ConsultScheduleViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */
/* @var $model_confirm */
/* @var $modelTeachers */

$this->title = Yii::t('art/guide', 'Consult Schedule');
$this->params['breadcrumbs'][] = $this->title;

$teachers_list = RefBook::find('teachers_fio')->getList();
$auditory_list = RefBook::find('auditory_memo_1')->getList();

$noteModel = NoticeConsultDisplay::getData($dataProvider->models, $model_date->plan_year);
$readonly = ($noteModel->confirmIsAvailable() && Teachers::isOwnTeacher($modelTeachers->id)) ? false : true;
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
        'value' => function ($model, $key, $index, $widget) {
            return $model->sect_name ? $model->sect_name . $model->getSectNotice() : null;
        },
        'label' => Yii::t('art/guide', 'Sect') . '/' . Yii::t('art/student', 'Student'),
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
        'footer' => 'ИТОГО: ак.час',
    ],
    [
        'attribute' => 'year_time_consult',
        'value' => function ($model) {
            return $model->year_time_consult;
        },
        'group' => true,
        'subGroupOf' => 2,
        'footer' => $noteModel->getTotal('year_time_consult'),
    ],
    [
        'attribute' => 'direction_id',
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'teachers_id',
        'value' => function ($model) use ($teachers_list) {
            return $teachers_list[$model->teachers_id] ?? '';
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2,
    ],
    [
        'attribute' => 'load_time_consult',
        'value' => function ($model) use ($noteModel) {
            return $model->load_time_consult . ' ' . $noteModel->getTeachersConsultOverLoadNotice($model);
        },
        'format' => 'raw',
        'footer' => $noteModel->getTotal('load_time_consult'),
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'datetime_in',
        'value' => function ($model) {
            return $model->datetime_in ? $model->date_in . '<br/>' . $model->time_in . '-' . $model->time_out : '';
        },
        'width' => '300px',
        'format' => 'raw',
        'footer' => $noteModel->getTotal('datetime_in'),
        'label' => 'Дата консультации'
    ],
    /*[
        'attribute' => 'datetime_in',
        'width' => '300px',
        'value' => function ($model) {
            return $model->datetime_in;
        },
        'format' => 'raw',
        'footer' => $noteModel->getTotal('datetime_in'),
    ],
    [
        'attribute' => 'datetime_out',
        'width' => '300px',
        'value' => function ($model) {
            return $model->datetime_out;
        },
        'format' => 'raw',
    ],*/
    [
        'attribute' => 'auditory_id',
        'width' => '350px',
        'value' => function ($model) use ($auditory_list, $noteModel) {
            $auditory = $auditory_list[$model->auditory_id] ?? '';
            return $auditory != '' ? $auditory . $noteModel->getConsultOverLoopingNotice($model) : '';
        },

        'format' => 'raw',
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
                    ['/teachers/default/consult-items', 'id' => $model->teachers_id, 'load_id' => $model->teachers_load_id, 'mode' => 'create'], [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/teachers/default/consult-items', 'id' => $model->teachers_id, 'objectId' => $model->consult_schedule_id, 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/teachers/default/consult-items', 'id' => $model->teachers_id, 'objectId' => $model->consult_schedule_id, 'mode' => 'delete'], [
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
            'create' => function ($model) use ($noteModel) {
                return $noteModel->getTeachersConsultScheduleNeed($model);
            },
            'delete' => function ($model) {
                return $model->consult_schedule_id !== null;
            },
            'update' => function ($model) {
                return $model->consult_schedule_id !== null;
            }
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'visible' => \artsoft\Art::isFrontend() && Teachers::isOwnTeacher($modelTeachers->id) && in_array($model_confirm->confirm_status, [0, 3]),
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    ['/teachers/consult-items/create', 'load_id' => $model->teachers_load_id], [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/teachers/consult-items/update', 'id' => $model->consult_schedule_id], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/teachers/consult-items/delete', 'id' => $model->consult_schedule_id], [
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
            'create' => function ($model) use ($noteModel) {
                return $noteModel->getTeachersConsultScheduleNeed($model);
            },
            'delete' => function ($model) use ($model_confirm) {
                return $model->consult_schedule_id !== null;
            },
            'update' => function ($model) use ($model_confirm) {
                return $model->consult_schedule_id !== null;
            }
        ],
    ],
];
?>
<div class="consult-schedule-index">
    <div class="panel">

        <div class="panel-heading">
            Расписание консультаций: <?php echo RefBook::find('teachers_fullname')->getValue($modelTeachers->id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <?= \artsoft\Art::isBackend() ? $this->render('_confirm', compact('model_confirm', 'readonly')) : ''; ?>
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
                        <?= Html::a('<i class="fa fa-calendar" aria-hidden="true"></i> Отчет по расписанию консультаций',
                            ['/reports/default/teachers-consult', 'id' => $modelTeachers->id],
                            [
                                'target' => '_blank',
                                'class' => 'btn btn-warning',
                            ]); ?>
                    <?php endif; ?>
                </div>
                <div class="col-sm-12">
                    <?php echo \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info-circle"></i> Совет: Для быстрого перемещения по строкам вправо, используйте колесико мышки и нажатую кнопку "Shift".',
                        'options' => ['class' => 'alert-info'],
                    ]);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6 text-right">
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'consult-schedule-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'consult-schedule-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'consult-schedule-grid',
                'pjax' => false,
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'showPageSummary' => false,
                'showFooter' => true,
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                            ['content' => 'Нагрузка', 'options' => ['colspan' => 3, 'class' => 'text-center info']],
                            ['content' => 'Расписание консультаций', 'options' => ['colspan' => 3, 'class' => 'text-center danger']],
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

