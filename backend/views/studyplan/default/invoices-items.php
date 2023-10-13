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
    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px'], 'checkboxOptions' => function ($model, $key, $index, $column) {
        return ['value' => $model->studyplan_invoices_id];
    },
        'visible' => false,
    ],
    [
        'attribute' => 'student_id',
        'value' => function ($model) {
            return sprintf('#%06d', $model->student_id);
        },
        'label' => 'ФЛС',
        'group' => true,  // enable grouping
    ],
    [
        'attribute' => 'student_fio',
        'width' => '310px',
        'value' => function ($model, $key, $index, $widget) {

            return $model->student_fio;
        },
        'group' => true,  // enable grouping
    ],
    [
        'attribute' => 'programm_id',
        'value' => function ($model) {
            return $model->programm_short_name;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    /*[
        'attribute' => 'education_cat_id',
        'value' => function ($model) {
            return $model->education_cat_short_name;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],*/
    [
        'attribute' => 'course',
        'value' => function ($model) {
            return \artsoft\helpers\ArtHelper::getCourseList()[$model->course];
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'studyplan_subjects',
        'value' => function ($model) {
            $v = [];
            foreach (explode(',', $model->studyplan_subjects) as $studyplan_subject) {
                if (!$studyplan_subject) {
                    continue;
                }
                $v[] = $studyplan_subject;
            }
            return implode('<br/> ', $v);
        },
        'width' => '400px',
        'format' => 'raw',
        'noWrap' => false,
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'invoices_summ',
        'value' => function ($model) {
            return $model->month_time_fact . ' ' . $model->invoices_summ;
        },
        'contentOptions' => function ($model) {
            switch ($model->studyplan_invoices_status) {
                case StudyplanInvoices::STATUS_WORK:
                    return ['class' => 'default'];
                case StudyplanInvoices::STATUS_PAYD:
                    return ['class' => 'info'];
                case StudyplanInvoices::STATUS_RECEIPT:
                    return ['class' => 'success'];
                case StudyplanInvoices::STATUS_ARREARS:
                    return ['class' => 'danger'];
                default:
                    return [];
            }
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'invoices_reporting_month',
        'label' => 'Период'
    ],
    [
        'class' => 'artsoft\grid\columns\StatusColumn',
        'attribute' => 'studyplan_invoices_status',
        'optionsArray' => [
            [StudyplanInvoices::STATUS_WORK, 'Счет в работе', 'default'],
            [StudyplanInvoices::STATUS_PAYD, 'Счет оплачен', 'info'],
            [StudyplanInvoices::STATUS_RECEIPT, 'Поступили средства', 'success'],
            [StudyplanInvoices::STATUS_ARREARS, 'Задолженность по оплате', 'danger'],
        ],
        'options' => ['style' => 'width:120px']
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'visible' => \artsoft\Art::isBackend(),
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete} {view} {print}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    ['/studyplan/default/studyplan-invoices', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_invoices_id, 'mode' => 'create'], [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/studyplan/default/studyplan-invoices', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_invoices_id, 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/studyplan/default/studyplan-invoices', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_invoices_id, 'mode' => 'delete'], [
                        'title' => Yii::t('art', 'Delete'),
                        'aria-label' => Yii::t('art', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'print' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-print" aria-hidden="true" style="color: blue"></span>',
                    ['/invoices/default/make-invoices', 'id' => $model->studyplan_invoices_id], [
                        'title' => 'Скачать квитанцию',
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'view' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-qrcode" aria-hidden="true" style="color: red"></span>',
                    ['/studyplan/default/studyplan-invoices', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_invoices_id, 'mode' => 'view'], [
                        'title' => 'Оплатить по QR-коду',
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            }
        ],
        'visibleButtons' => [
            'create' => function ($model) {
                return $model->studyplan_invoices_id == null;
            },
            'delete' => function ($model) {
                return $model->studyplan_invoices_id !== null;
            },
            'update' => function ($model) {
                return $model->studyplan_invoices_id !== null;
            },
            'print' => function ($model) {
                return $model->studyplan_invoices_id !== null;
            },
            'view' => function ($model) {
                return $model->studyplan_invoices_id !== null;
            }
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'visible' => \artsoft\Art::isFrontend(),
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'template' => '{view}',
        'buttons' => [
            'print' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-print" aria-hidden="true" style="color: blue"></span>',
                    [\artsoft\models\User::hasRole(['student']) ? '/studyplan/default/make-invoices' : '/parents/studyplan/make-invoices', 'id' => $model->studyplan_invoices_id], [
                        'title' => 'Скачать квитанцию',
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'view' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-qrcode" aria-hidden="true" style="color: red"></span> Оплатить',
                    [\artsoft\models\User::hasRole(['student']) ? '/studyplan/default/studyplan-invoices' : '/parents/studyplan/studyplan-invoices', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_invoices_id, 'mode' => 'view'], [
                        'title' => 'Оплатить по QR-коду',
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            }
        ],
        'visibleButtons' => [
            'print' => function ($model) {
                return $model->studyplan_invoices_id !== null;
            },
            'view' => function ($model) {
                return $model->studyplan_invoices_id !== null;
            }
        ],
    ],
];
?>
<div class="studyplan-invoices-index">
    <div class="panel">
        <?= $this->render('_search_inv', compact('model_date', 'id')) ?>
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
                    <?= GridPageSize::widget(['pjaxId' => 'studyplan-invoices-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'studyplan-invoices-grid-pjax',
            ])
            ?>

            <?= GridView::widget([
                'id' => 'studyplan-invoices-grid',
                'pjax' => true,
                'dataProvider' => $dataProvider,
//                 'filterModel' => $searchModel,
//                'bulkActionOptions' => [
//                    'gridId' => 'studyplan-invoices-grid',
//                    'actions' => [
//                        Url::to(['bulk-delete']) => 'Удалить квитанции',
//                       /* Url::to(['bulk-load']) => 'Выгрузить квитанции в Word',
//                        Url::to(['bulk-new']) => 'Создать новые квитанции',*/
//                    ] //Configure here you bulk actions
//                ],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Ученик/Программа', 'options' => ['colspan' => 5, 'class' => 'text-center warning']],
                            ['content' => 'Счета за обучение', 'options' => ['colspan' => 4, 'class' => 'text-center success']],
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


