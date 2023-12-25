<?php

use artsoft\helpers\RefBook;
use common\widgets\editable\Editable;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\teachers\search\TeachersLoadViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */

$this->title = Yii::t('art/guide', 'Teachers Load');
$this->params['breadcrumbs'][] = $this->title;

//$sect_list = \common\models\teachers\Teachers::getSectListForTeachers($model->id, $model_date->plan_year);
$typeList = RefBook::find('subject_type_name')->getList();
$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'subject',
        'filter' => \common\models\teachers\TeachersLoadView::getSubjectListForTeachers($model->id, $model_date->plan_year),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'width' => '310px',
        'value' => function ($model, $key, $index, $widget) {
            return $model->subject;
        },
        'format' => 'raw',
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
        'attribute' => 'programm_id',
        'filter' => \common\models\teachers\TeachersLoadView::getProgrammListForTeachers($model->id, $model_date->plan_year),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'width' => '310px',
        'value' => function ($model, $key, $index, $widget) {
            return $model->education_programm_short_name;
        },
        'format' => 'raw',
        'group' => true,
        'subGroupOf' => 1,
        'footer' => 'Пед./Конц.',
    ],
    [
        'attribute' => 'week_time',
        'filter' => false,
        'value' => function ($model) {
            return $model->week_time;
        },
        'footer' => \common\models\teachers\TeachersLoadView::getTotal($dataProvider->models, 'week_time', $model->id),
    ],
    [
        'attribute' => 'year_time_consult',
        'filter' => false,
        'value' => function ($model) {
            return $model->year_time_consult;
        },
        'footer' => \common\models\teachers\TeachersLoadView::getTotal($dataProvider->models, 'year_time_consult', $model->id),
    ],
    [
        'attribute' => 'direction_id',
        'filter' => \common\models\guidejob\Direction::getDirectionShortList(),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],

        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->slug : null;
        },

        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'direction_vid_id',
        'filter' => \common\models\guidejob\DirectionVid::getDirectionVidShortList(),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],

        'value' => function ($model, $key, $index, $widget) {
            return $model->directionVid ? $model->directionVid->slug : null;
        },

        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'teachers_id',
        'filter' => false,
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 6,
    ],
    [
        'attribute' => 'load_time',
        'filter' => false,
        'value' => function ($model) {
            return $model->load_time . ' ' . $model->getItemLoadNotice();
        },
        'format' => 'raw',
        'footer' => \common\models\teachers\TeachersLoadView::getTotal($dataProvider->models, 'load_time', $model->id),
    ],
    [
        'attribute' => 'load_time_0',
        'filter' => false,
        'value' => function ($model) {
            return $model->load_time_0;
        },
        'format' => 'raw',
        'footer' => \common\models\teachers\TeachersLoadView::getTotal($dataProvider->models, 'load_time_0', $model->id),
        'contentOptions' => function ($model) {
            return $model->direction_vid_id == 1000 ? ['class' => 'success'] : ['class' => 'text-right success'];
        },
    ],
    [
        'attribute' => 'load_time_1',
        'filter' => false,
        'value' => function ($model) {
            return $model->load_time_1;
        },
        'format' => 'raw',
        'footer' => \common\models\teachers\TeachersLoadView::getTotal($dataProvider->models, 'load_time_1', $model->id),
        'contentOptions' => function ($model) {
            return $model->direction_vid_id == 1000 ? ['class' => 'warning'] : ['class' => 'text-right warning'];

        },
    ],
    [
        'attribute' => 'load_time_consult',
        'filter' => false,
        'value' => function ($model) {
            return $model->load_time_consult . ' ' . $model->getItemLoadConsultNotice();
        },
        'format' => 'raw',
        'footer' => \common\models\teachers\TeachersLoadView::getTotal($dataProvider->models, 'load_time_consult', $model->id),
        'contentOptions' => function ($model) {
            return $model->direction_vid_id == 1000 ? ['class' => 'info'] : ['class' => 'text-right info'];

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
                if ($model->subject_sect_studyplan_id == 0) {
                    return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                        Url::to(['/teachers/default/load-items', 'id' => $model->teachers_id, 'studyplan_subject_id' => $model->studyplan_subject_id, 'mode' => 'create']), [
                            'title' => Yii::t('art', 'Create'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]
                    );
                } else {
                    return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                        Url::to(['/teachers/default/load-items', 'id' => $model->teachers_id, 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id, 'mode' => 'create']), [
                            'title' => Yii::t('art', 'Create'),
                            'data-method' => 'post',
                            'data-pjax' => '0',

                        ]
                    );
                }
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    Url::to(['/teachers/default/load-items', 'id' => $model->teachers_id, 'objectId' => $model->teachers_load_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    Url::to(['/teachers/default/load-items', 'id' => $model->teachers_id, 'objectId' => $model->teachers_load_id, 'mode' => 'delete']), [
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
//            'create' => function ($model) {
//                return $model->getTeachersLoadsNeed();
//            },
            'delete' => function ($model) {
                return $model->teachers_load_id !== null;
            },
            'update' => function ($model) {
                return $model->teachers_load_id !== null;
            }
        ],
    ],
];
?>
<div class="subject-load-index">
    <div class="panel">
        <div class="panel-heading">
            Нагрузка: <?php echo RefBook::find('teachers_fullname')->getValue($model->id); ?>
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'subject-load-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'subject-load-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'subject-load-grid',
                'pjax' => true,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'showPageSummary' => false,
                'showFooter' => true,
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет/Группа/Ученик', 'options' => ['colspan' => 6, 'class' => 'text-center warning']],
                            ['content' => 'Нагрузка', 'options' => ['colspan' => 8, 'class' => 'text-center info']],
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

