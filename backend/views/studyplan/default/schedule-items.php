<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;
use artsoft\helpers\NoticeDisplay;
use common\models\schedule\SubjectScheduleView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Schedule');
$this->params['breadcrumbs'][] = $this->title;

$teachers_list = RefBook::find('teachers_fio')->getList();
$auditory_list = RefBook::find('auditory_memo_1')->getList();
$noteModel = NoticeDisplay::getData($dataProvider->models, $model->plan_year);

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
        'width' => '320px',
        'value' => function ($model) {
            return $model->sect_name ? $model->sect_name . $model->getSectNotice() : null;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
        'format' => 'raw',
    ],
    [
        'attribute' => 'week_time',
        'value' => function ($model) {
            return $model->week_time;
        },
        'group' => true,
        'subGroupOf' => 1,
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
        'attribute' => 'teachers_id',
        'value' => function ($model)  use($teachers_list) {
            $teachers_fio = $teachers_list[$model->teachers_id] ?? '';
            return \artsoft\Art::isBackend() ? Html::a($teachers_fio,
                ['/teachers/default/schedule-items', 'id' => $model->teachers_id],
                [
                    'target' => '_blank',
                    'data-pjax' => '0',
//                    'class' => 'btn btn-info',
                ]) : $teachers_fio;
        },
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'load_time',
        'value' => function ($model) {
            return $model->load_time /*. ' ' . $model->getItemLoadStudyplanNotice()*/ ;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 4,
        'format' => 'raw',
    ],
    [
        'attribute' => 'scheduleDisplay',
        'value' => function (SubjectScheduleView $model) use ($noteModel) {
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
        'template' => '{create} {update} {delete}',
        'visible' => \artsoft\Art::isBackend(),
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/schedule-items', 'id' => $model->studyplan_id, 'load_id' => $model->teachers_load_id, 'mode' => 'create']), [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/schedule-items', 'id' => $model->studyplan_id, 'objectId' => $model->subject_schedule_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/schedule-items', 'id' => $model->studyplan_id, 'objectId' => $model->subject_schedule_id, 'mode' => 'delete']), [
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
            'create' => function (SubjectScheduleView $model) use ($noteModel) {
                return $noteModel->getTeachersScheduleNeed($model) && $model->subject_sect_studyplan_id === 0;
            },
            'delete' => function ($model) {
                return $model->subject_schedule_id !== null && $model->subject_sect_studyplan_id === 0;
            },
            'update' => function ($model) {
                return $model->subject_schedule_id !== null && $model->subject_sect_studyplan_id === 0;
            }
        ]
    ],
];
?>
<div class="subject-schedule-index">
    <div class="panel">
        <div class="panel-heading">
            Элементы расписания: <?= RefBook::find('students_fullname')->getValue($model->student_id); ?>
            <?= $model->getProgrammName() . ' - ' . $model->course . ' класс.'; ?>
        </div>
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'subject-schedule-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'subject-schedule-grid-pjax',
            ])
            ?>
            <?= GridView::widget([
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
                            ['content' => 'Расписание занятий', 'options' => ['colspan' => 4, 'class' => 'text-center danger']],
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

