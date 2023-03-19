<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;
use common\models\subjectsect\SubjectScheduleStudyplanView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\ConsultScheduleViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */
/* @var $modelTeachers */

$this->title = Yii::t('art/guide', 'Consult Schedule');
$this->params['breadcrumbs'][] = $this->title;

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
        'label' =>  Yii::t('art/guide', 'Sect').'/'.Yii::t('art/student', 'Student'),
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
    ],
    [
        'attribute' => 'year_time_consult',
        'value' => function ($model) {
            return $model->year_time_consult;
        },
        'group' => true,
        'subGroupOf' => 2,
        'pageSummaryFunc' => GridView::F_SUM
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
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'load_time_consult',
        'value' => function ($model) {
            return $model->load_time_consult . ' ' . $model->getItemLoadConsultNotice();
        },
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'datetime_in',
        'width' => '300px',
        'value' => function ($model) {
            return $model->datetime_in;
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'datetime_out',
        'width' => '300px',
        'value' => function ($model) {
            return $model->datetime_out;
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'auditory_id',
        'width' => '300px',
        'value' => function ($model) {
            return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    Url::to(['/teachers/default/consult-items', 'id' => $model->teachers_id, 'load_id' => $model->teachers_load_id, 'mode' => 'create']), [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    Url::to(['/teachers/default/consult-items', 'id' => $model->teachers_id, 'objectId' => $model->consult_schedule_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    Url::to(['/teachers/default/consult-items', 'id' => $model->teachers_id, 'objectId' => $model->consult_schedule_id, 'mode' => 'delete']), [
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
                return $model->getTeachersConsultNeed();
            },
            'delete' => function ($model) {
                return $model->consult_schedule_id !== null;
            },
            'update' => function ($model) {
                return $model->consult_schedule_id !== null;
            }
        ],
    ],
];
?>
<div class="consult-schedule-index">
    <div class="panel">

        <div class="panel-heading">
            Расписание консультаций: <?php echo RefBook::find('teachers_fio')->getValue($modelTeachers->id); ?>
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
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                            ['content' => 'Нагрузка', 'options' => ['colspan' => 3, 'class' => 'text-center info']],
                            ['content' => 'Расписание консультаций', 'options' => ['colspan' => 4, 'class' => 'text-center danger']],
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

