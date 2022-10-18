<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schoolplan\search\SchoolplanProtocolItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Schoolplan Protocol Items');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    'schoolplan_protocol_id',
    'studyplan_subject_id',
    'thematic_items_list',
    'lesson_mark_id',
    'winner_id',
    'resume',
    'status_exe',
    'status_sign',
    'signer_id',
//    [
//        'attribute' => 'subject_cat_id',
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => RefBook::find('subject_category_name')->getList(),
//        'value' => function ($model) {
//            return RefBook::find('subject_category_name')->getValue($model->subject_cat_id ?? null);
//        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
//        'format' => 'raw',
//        'group' => true,
//    ],
//    [
//        'attribute' => 'subject_id',
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => RefBook::find('subject_name')->getList(),
//        'value' => function ($model) {
//            return RefBook::find('subject_name')->getValue($model->subject_id ?? null);
//        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
//        'format' => 'raw',
//        'group' => true,
//    ],
//    [
//        'attribute' => 'subject_type_id',
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => RefBook::find('subject_type_name')->getList(),
//        'value' => function ($model) {
//            return RefBook::find('subject_type_name')->getValue($model->subject_type_id ?? null);
//        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
//        'format' => 'raw',
//        'group' => true,
//    ],
//    [
//        'attribute' => 'subject_vid_id',
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => RefBook::find('subject_vid_name')->getList(),
//        'value' => function ($model) {
//            return RefBook::find('subject_vid_name_dev')->getValue($model->subject_vid_id);
//        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
//        'format' => 'raw',
//        'group' => true,
//        'subGroupOf' => 1
//    ],
//    [
//        'attribute' => 'teachers_id',
//        'filterType' => GridView::FILTER_SELECT2,
//        'filter' => RefBook::find('teachers_fio')->getList(),
//        'value' => function ($model) {
//            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
//        },
//        'filterWidgetOptions' => [
//            'pluginOptions' => ['allowClear' => true],
//        ],
//        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
//    ],
//    'description:text',
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                    Url::to(['/studyplan/default/protocol-items', 'id' => $model->studyplan_id, 'studyplan_subject_id' => $model->studyplan_subject_id, 'mode' => 'create']), [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<i class="fa fa-edit" aria-hidden="true"></i>',
                    Url::to(['/studyplan/default/protocol-items', 'id' => $model->studyplan_id, 'objectId' => $model->subject_protocol_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                    Url::to(['/studyplan/default/protocol-items', 'id' => $model->studyplan_id, 'objectId' => $model->subject_protocol_id, 'mode' => 'delete']), [
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
                return $model->subject_protocol_id;
            },
            'update' => function ($model) {
                return $model->subject_protocol_id;
            }
        ],
    ],
];
?>
<div class="subject-protocol-index">
    <div class="panel">
        <div class="panel-heading">
           Выполнение плана и участие в мероприятиях
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'subject-protocol-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'subject-protocol-grid-pjax',
            ])
            ?>
            <?=
            GridView::widget([
                'id' => 'subject-protocol-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table-condensed'],
                'columns' => $columns,
//                'beforeHeader' => [
//                    [
//                        'columns' => [
//                            ['content' => 'Дисциплина', 'options' => ['colspan' => 5, 'class' => 'text-center warning']],
//                            ['content' => 'Характеристика', 'options' => ['colspan' => 3, 'class' => 'text-center danger']],
//                        ],
//                        'options' => ['class' => 'skip-export'] // remove this row from export
//                    ]
//                ],
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

