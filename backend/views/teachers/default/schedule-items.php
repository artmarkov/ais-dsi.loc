<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */

$this->title = Yii::t('art/guide', 'Subject Schedule');
$this->params['breadcrumbs'][] = $this->title;

$sect_list = \common\models\teachers\Teachers::getSectListForTeachers($model->id, $model_date->plan_year);

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'filter' => false,
        'value' => function ($model) {
            return $model->subject_sect_studyplan_id != 0 ? RefBook::find('sect_name_4')->getValue($model->subject_sect_id) : RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id);
        },
        'group' => true,
    ],
    [
        'attribute' => 'subject_sect_studyplan_id',
        'width' => '310px',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => $sect_list,
        'value' => function ($model, $key, $index, $widget) {
            return $model->subject_sect_studyplan_id === 0 ? 'Индивидуально' :
                ($model->subject_sect_studyplan_id != null ? RefBook::find('sect_name_1')->getValue($model->subject_sect_studyplan_id) . $model->getSectNotice() : null);
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
        'format' => 'raw',
    ],
    [
        'attribute' => 'week_time',
        'filter' => false,
        'value' => function ($model) {
            return $model->week_time;
        },
        'group' => true,
        'subGroupOf' => 2,
    ],
    [
        'attribute' => 'studyplan_subject_list',
        'width' => '210px',
        'filter' => false,
        'value' => function ($model, $key, $index, $widget) {
            $data = [];
            if (!empty($model->studyplan_subject_list)) {
                foreach (explode(',', $model->studyplan_subject_list) as $item => $studyplan_subject_id) {
                    $data[] = RefBook::find('studyplan_subject-student_fio')->getValue($studyplan_subject_id);
                }
            }
            return implode(',', $data);
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'direction_id',
        'filter' => false,
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'teachers_id',
        'filter' => false,
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 5
    ],
    [
        'attribute' => 'load_time',
        'filter' => false,
        'value' => function ($model) {
            return $model->load_time . ' ' . $model->getItemLoadNotice();
        },
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 5
    ],
    [
        'attribute' => 'scheduleDisplay',
        'width' => '310px',
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
            <div class="panel panel-default">
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
                            <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'subject-schedule-grid-pjax']) ?>
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
                        'filterModel' => $searchModel,
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
                    ]);
                    ?>
                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

