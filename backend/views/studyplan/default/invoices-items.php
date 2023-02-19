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
    ['class' => 'artsoft\grid\CheckboxColumn',  'options' => ['style' => 'width:10px'], 'checkboxOptions' => function ($model, $key, $index, $column) {
        return ['value' => $model->studyplan_invoices_id];
    }
      ],
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'student_id',
        'value' => function ($model) {
            return sprintf('#%06d', $model->student_id);
        },
        'label' => 'ФЛС',
        'group' => true,  // enable grouping
    ],
    [
        'attribute' => 'studentFio',
        'width' => '310px',
        'value' => function ($model, $key, $index, $widget) {

            return RefBook::find('students_fio')->getValue($model->student_id);
        },
        'group' => true,  // enable grouping
    ],
    [
        'attribute' => 'programm_id',
        'value' => function ($model) {
            return RefBook::find('education_programm_short_name')->getValue($model->programm_id);
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'education_cat_id',
        'value' => function ($model) {
            return RefBook::find('education_cat_short')->getValue($model->education_cat_id);
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'course',
        'value' => function ($model) {
            return \artsoft\helpers\ArtHelper::getCourseList()[$model->course];
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'studyplan_subject_ids',
        'value' => function ($model) {
            $v = [];
            foreach (explode(',', $model->studyplan_subject_ids) as $studyplan_subject_id) {
                if (!$studyplan_subject_id) {
                    continue;
                }
                $v[] = RefBook::find('subject_memo_3')->getValue($studyplan_subject_id);
            }
            return implode('<br/> ', $v);
        },
        'width' => '400px',
        'format' => 'raw',
        'noWrap' => true,
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
                    return ['class' => 'warning'];
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
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/studyplan-invoices', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_invoices_id,  'mode' => 'create']), [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/studyplan-invoices', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_invoices_id,  'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    Url::to(['/studyplan/default/studyplan-invoices', 'id' => $model->studyplan_id, 'objectId' => $model->studyplan_invoices_id,  'mode' => 'delete']), [
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
                return $model->studyplan_invoices_id !== null;
            },
            'update' => function ($model) {
                return $model->studyplan_invoices_id !== null;
            }
        ],
    ],
];
?>
<div class="studyplan-invoices-index">
    <div class="panel">
        <?= $this->render('_search_inv', compact('model_date')) ?>
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
               // 'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'studyplan-invoices-grid',
                    'actions' => [
                        Url::to(['bulk-delete']) => 'Удалить квитанции',
                        Url::to(['bulk-load']) => 'Выгрузить квитанции в Excel',
                        Url::to(['bulk-new']) => 'Создать новые квитанции',
                    ] //Configure here you bulk actions
                ],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Ученик/Программа', 'options' => ['colspan' => 7, 'class' => 'text-center warning']],
                            ['content' => 'Дисциплина/Преподаватель', 'options' => ['colspan' => 1, 'class' => 'text-center info']],
                            ['content' => 'Счета за обучение', 'options' => ['colspan' => 2, 'class' => 'text-center success']],
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


