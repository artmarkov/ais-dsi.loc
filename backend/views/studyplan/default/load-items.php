<?php

use artsoft\helpers\RefBook;
use common\models\education\LessonItems;
use common\models\subjectsect\SubjectScheduleTeachersView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subjectsect\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Teachers Load');
$this->params['breadcrumbs'][] = $this->title;

$addLoads = function ($model, $key, $index, $widget) {
    $content = [];
    if ($model->getTeachersLoadsNeed()) {
        if ($model->subject_sect_studyplan_id == 0) {
            $content += [4 =>  Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                Url::to(['/studyplan/default/load-items', 'id' => $model->studyplan_id, 'studyplan_subject_id' => $model->studyplan_subject_id, 'mode' => 'create']), [
                    'title' => Yii::t('art', 'Create'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'disabled' => true
                ]
            )];
        } else {
            $content += [4 =>  Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                Url::to(['/studyplan/default/load-items', 'id' => $model->studyplan_id, 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id, 'mode' => 'create']), [
                    'title' => Yii::t('art', 'Create'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'disabled' => true
                ]
            )];
        }
    }
    return [
       // 'mergeColumns' => [[1, 4]],
        'content' => $content,
        'contentOptions' => [      // content html attributes for each summary cell
            4 => ['class' => 'text-right text-end'],
        ],
        'options' => ['class' => 'info h-25 text-center']
    ];
};
$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'value' => function ($model) {
            return RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id ?? null);;
        },
        'group' => true,
        'groupFooter' => $addLoads
    ],
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
//    [
//        'attribute' => 'week_time',
//        'value' => function ($model) {
//            return $model->week_time;
//        },
//        'group' => true,
//        'subGroupOf' => 0,
//    ],
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

    ],
    [
        'attribute' => 'load_time',
        'value' => function ($model) {
            return $model->load_time /*. ' ' . $model->getTeachersOverLoadNotice()*/ ;
        },
        'format' => 'raw',

    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'update' => function ($key, $model) {
                return Html::a('<i class="fa fa-edit" aria-hidden="true"></i>',
                    Url::to(['/studyplan/default/load-items', 'id' => $model->studyplan_id, 'objectId' => $model->teachers_load_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                    Url::to(['/studyplan/default/load-items', 'id' => $model->studyplan_id, 'objectId' => $model->teachers_load_id, 'mode' => 'delete']), [
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
<div class="teachers-load-index">
    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table-condensed'],
//                        'showPageSummary' => true,
                        'pjax' => true,
                        'hover' => true,
                        'panel' => [
                            'heading' => 'Нагрузка',
                            'type' => 'default',
                            'after' => '',
                        ],
                        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
                        'columns' => $columns,
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Дисциплина/Группа', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                                    ['content' => 'Нагрузка', 'options' => ['colspan' => 4, 'class' => 'text-center info']],
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
                                    Url::to(['/studyplan/default/load-items', 'id' => $id]), [
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
                </div>
            </div>
        </div>
    </div>
</div>

