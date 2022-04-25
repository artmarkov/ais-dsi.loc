<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\studyplan\StudyplanInvoices;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\StudyplanInvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Studyplan Invoices');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'value' => function ($model) {
            return RefBook::find('subject_memo_1')->getValue($model->studyplan_subject_id);
        },
        'group' => true,
    ],

    'subject_type_id',

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
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => \common\models\guidejob\Direction::getDirectionList(),
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],

        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'teachers_id',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => false /*RefBook::find('teachers_fio')->getList()*/,
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
        'group' => true,  // enable grouping
        'subGroupOf' => 5
    ],
    [
        'attribute' => 'load_time',
        'value' => function ($model) {
            return $model->load_time;
        },
        'format' => 'raw',
    ],

    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                    return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                        Url::to(['/invoices/default/create']), [
                            'title' => Yii::t('art', 'Create'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]
                    );
            },
            'update' => function ($key, $model) {
                return Html::a('<i class="fa fa-edit" aria-hidden="true"></i>',
                    Url::to(['/invoices/default/update', 'id' => $model->studyplan_invoices_id]), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                    Url::to(['/invoices/default/delete', 'id' => $model->studyplan_invoices_id]), [
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
//            'delete' => function ($model) {
//                return $model->teachers_load_id !== null;
//            },
//            'update' => function ($model) {
//                return $model->teachers_load_id !== null;
//            }
        ],
    ],
];
?>
<div class="studyplan-invoices-index">
    <div class="panel">
        <div class="panel-heading">
            Счета за обучение
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php 
                    /* Uncomment this to activate GridQuickLinks */
                    /* echo GridQuickLinks::widget([
                        'model' => StudyplanInvoices::className(),
                        'searchModel' => $searchModel,
                    ])*/
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?=  GridPageSize::widget(['pjaxId' => 'studyplan-invoices-grid-pjax']) ?>
                </div>
            </div>

                    <?php 
                    Pjax::begin([
                        'id' => 'studyplan-invoices-grid-pjax',
                    ])
                    ?>

                    <?= GridView::widget([
                        'id' => 'studyplan-invoices-grid',
                        'pjax' => false,
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => $columns,
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Дисциплина/Группа', 'options' => ['colspan' => 6, 'class' => 'text-center warning']],
                                    ['content' => 'Счета за обучение', 'options' => ['colspan' => 5, 'class' => 'text-center info']],
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


