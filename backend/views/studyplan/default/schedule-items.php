<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Schedule');
$this->params['breadcrumbs'][] = $this->title;

//$studyplan_subject_list = \common\models\studyplan\Studyplan::getSubjectListForStudyplan($model->id);
//$subject_sect_studyplan_list = \common\models\studyplan\Studyplan::getSectListForStudyplan($model->id);

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => $studyplan_subject_list,
        'value' => function ($model) {
            return RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id);
        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,
    ],
    [
        'attribute' => 'sect_name',
        'width' => '320px',
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => $subject_sect_studyplan_list,
        'value' => function ($model) {
            return $model->sect_name != 'Индивидуально' ? $model->sect_name . $model->getSectNotice() : $model->sect_name;
        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
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
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => \common\models\guidejob\Direction::getDirectionList(),
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,  // enable grouping
        'subGroupOf' => 3
    ],
    [
        'attribute' => 'teachers_id',
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => RefBook::find('teachers_fio')->getList(),
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'load_time',
        'value' => function ($model) {
            return $model->load_time . ' ' . $model->getItemLoadNotice();
        },
         'group' => true,  // enable grouping
        'subGroupOf' => 4,
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
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => RefBook::find('auditory_memo_1', 1, true)->getList(),
        'value' => function ($model) {
            return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete}',
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
        <div class="panel-heading">
            Элементы расписания: <?= RefBook::find('students_fio')->getValue($model->student_id);?>
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
                            ['content' => 'Дисциплина', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
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

