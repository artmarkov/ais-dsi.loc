<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;
use common\models\studyplan\StudyplanThematic;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\ThematicViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Thematic plans');
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
        'width' => '320px',
        'value' => function ($model) {
            return $model->sect_name ? $model->sect_name : null;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
        'format' => 'raw',
    ],
    [
        'attribute' => 'thematic_category',
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => \common\models\studyplan\StudyplanThematic::getCategoryList(),
        'value' => function ($model) {
            return StudyplanThematic::getCategoryValue($model->thematic_category);
        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'format' => 'raw',
//        'group' => true,
//        'subGroupOf' => 1
    ],
    [
        'attribute' => 'half_year',
        'value' => function (StudyplanThematic $model) {
            return \artsoft\helpers\ArtHelper::getHalfYearValue($model->half_year);
        },
        'options' => ['style' => 'width:150px'],
        'format' => 'raw',
    ],
    [
        'attribute' => 'doc_status',
        'filter' => StudyplanThematic::getDocStatusList(),
        'value' => function (StudyplanThematic $model) {
            return StudyplanThematic::getDocStatusValue($model->doc_status);
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'doc_sign_teachers_id',
        'filter' => RefBook::find('teachers_fio')->getList(),
        'value' => function (StudyplanThematic $model) {
            return RefBook::find('teachers_fio')->getValue($model->doc_sign_teachers_id);
        },
        'options' => ['style' => 'width:150px'],
        'format' => 'raw',
    ],
    [
        'attribute' => 'doc_sign_timestamp',
        'value' => function (StudyplanThematic $model) {
            return Yii::$app->formatter->asDatetime($model->doc_sign_timestamp);
        },
        'options' => ['style' => 'width:150px'],
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'visible' => \artsoft\Art::isBackend(),
        'width' => '90px',
        'template' => '{create} {view} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                if ($model->subject_sect_studyplan_id == null) {
                    return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                       ['/teachers/default/thematic-items', 'id' => $model->teachers_id, 'studyplan_subject_id' => $model->studyplan_subject_id, 'mode' => 'create'], [
                            'title' => Yii::t('art', 'Create'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'disabled' => true
                        ]
                    );
                } else {
                    return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                       ['/teachers/default/thematic-items', 'id' => $model->teachers_id, 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id, 'mode' => 'create'], [
                            'title' => Yii::t('art', 'Create'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'disabled' => true
                        ]
                    );
                }

            },
            'view' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                    ['/teachers/default/thematic-items', 'id' => $model->teachers_id, 'objectId' => $model->studyplan_thematic_id, 'mode' => 'view'], [
                        'title' => Yii::t('art', 'View'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/teachers/default/thematic-items', 'id' => $model->teachers_id, 'objectId' => $model->studyplan_thematic_id, 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/teachers/default/thematic-items', 'id' => $model->teachers_id, 'objectId' => $model->studyplan_thematic_id, 'mode' => 'delete'], [
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
                return true;
            },
            'delete' => function ($model) {
                return $model->studyplan_thematic_id;
            },
            'update' => function ($model) {
                return $model->studyplan_thematic_id;
            },
            'view' => function ($model) {
                return $model->studyplan_thematic_id;
            }
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'visible' => \artsoft\Art::isFrontend(),
        'width' => '90px',
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                if ($model->subject_sect_studyplan_id == null) {
                    return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                        ['/teachers/thematic-items/create', 'studyplan_subject_id' => $model->studyplan_subject_id], [
                            'title' => Yii::t('art', 'Create'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'disabled' => true
                        ]
                    );
                } else {
                    return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                        ['/teachers/thematic-items/create', 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id], [
                            'title' => Yii::t('art', 'Create'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'disabled' => true
                        ]
                    );
                }

            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/teachers/thematic-items/update', 'id' => $model->studyplan_thematic_id], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/teachers/thematic-items/delete', 'id' => $model->studyplan_thematic_id], [
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
                return true;
            },
            'delete' => function ($model) {
                return $model->studyplan_thematic_id;
            },
            'update' => function ($model) {
                return $model->studyplan_thematic_id;
            },
            'view' => function ($model) {
                return $model->studyplan_thematic_id;
            }
        ],
    ],
];
?>
<div class="teachers-thematic-index">
    <div class="panel">
        <div class="panel-heading">
            Тематические планы:  <?php echo RefBook::find('teachers_fio')->getValue($model->id); ?>
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'studyplan-thematic-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'studyplan-thematic-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'studyplan-thematic-grid',
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет/Группа', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                            ['content' => 'План', 'options' => ['colspan' => 6, 'class' => 'text-center danger']],
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

