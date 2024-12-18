<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\studyplan\StudyplanInvoices;
use artsoft\helpers\Html;
use common\models\studyplan\Studyplan;

/* @var $this yii\web\View */
/* @var $searchModel common\models\studyplan\search\StudyplanInvoicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model_date */

$this->title = Yii::t('art/studyplan', 'Studyplan Invoices');
$this->params['breadcrumbs'][] = $this->title;
$invModel = \artsoft\helpers\InvoicesHelper::getData($dataProvider->models, $model_date);

$columns = [
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
        'value' => function ($model, $key, $index, $widget) {
            $str = '';
            $arr = [];
            $str .= \artsoft\Art::isBackend() ? Html::a($model->student_fio,
                ['/studyplan/default/view', 'id' => $model->studyplan_id],
                [
                    'target' => '_blank',
                    'data-pjax' => '0',
                    // 'class' => 'btn btn-link',
                    'title' => 'Открыть в новом окне'
                ]) : $model->student_fio;

            if ($model->limited_status_list) {
                foreach (explode(',', $model->limited_status_list) as $limited_status) {
                    $arr[] = \common\models\students\Student::getLimitedStatusValue($limited_status);
                }
                $str .= ' <span class="label label-warning">' . implode(', ', $arr) . '</span>';
            }
            return $str;
        },
        'format' => 'raw',
        'group' => true,  // enable grouping
    ],
    [
        'attribute' => 'education_cat_id',
        'value' => function ($model) {
            return $model->education_cat_short_name;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'programm_id',
        'value' => function ($model) {
            return $model->programm_short_name;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'status',
        'value' => function ($model) {
            $val = Studyplan::getStatusValue($model->status);
            return $model->status == Studyplan::STATUS_ACTIVE ? '<span class="label label-success">' . $val . '</span>' : '<span class="label label-danger">' . $val . '</span>';
        },
        'contentOptions' => ['style' => "text-align:center; vertical-align: middle;"],
        /* 'contentOptions' => function ($model) {
             switch ($model->status) {
                 case Studyplan::STATUS_ACTIVE:
                     return ['class' => 'default'];
                 case Studyplan::STATUS_INACTIVE:
                     return ['class' => 'danger'];
                 default:
                     return [];
             }
         },*/
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'course',
        'value' => function ($model) {
            return \artsoft\helpers\ArtHelper::getCourseList()[$model->course];
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 4
    ],
    [
        'attribute' => 'subject_list',
        'value' => function ($model) use ($invModel) {
            return $invModel->getSubjects($model);
        },
        'width' => '400px',
        'format' => 'raw',
        'noWrap' => false,
        'group' => true,  // enable grouping
        'subGroupOf' => 4,
        'footer' => 'ИТОГО: оплачено/неоплачено(руб)'
    ],
    ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px'], 'checkboxOptions' => function ($model, $key, $index, $column) {
        return ['value' => $model->studyplan_id . '|' . $model->studyplan_invoices_id];
    },
        'visible' => \artsoft\Art::isBackend(),
    ],
    [
        'attribute' => 'invoices_summ',
        'value' => function ($model) {
            return $model->invoices_summ . ($model->mat_capital_flag == 1 ? '<span style="color:red">мк</span>' : '');
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
        'footer' => \common\models\studyplan\StudyplanInvoicesView::getTotalSumm($dataProvider->models),
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
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{create} {update} {delete} {view} {print}',
        'buttons' => [
            'create' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                    ['/invoices/default/create', 'studyplan_id' => $model->studyplan_id], [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/invoices/default/update', 'id' => $model->studyplan_invoices_id], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/invoices/default/delete', 'id' => $model->studyplan_invoices_id], [
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
                    ['/invoices/default/view', 'id' => $model->studyplan_invoices_id], [
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
];
?>
    <div class="studyplan-invoices-index">
        <div class="panel">
            <?= $this->render('_search', compact('model_date', 'plan_year')) ?>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php if (\artsoft\Art::isBackend()): ?>
                            <?= \yii\bootstrap\Alert::widget([
                                'body' => '<i class="fa fa-info"></i> Для добавления нескольких квитанций, нажмите чекбоксы справа.',
                                'options' => ['class' => 'alert-info'],
                            ]);
                            ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6">
                        <?php echo Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Создать новые квитанции',
                            ['new'],
                            [
                                // 'target' => '_blank',
                                'data-pjax' => false,
                                'class' => 'btn btn-success bulk-new disabled',
                                'visible' => true,
                                //    'title' => 'Открыть в новом окне'
                            ]);
                        ?>
                        <?php
                        /* Uncomment this to activate GridQuickLinks */
                        /* echo GridQuickLinks::widget([
                            'model' => StudyplanInvoices::className(),
                            'searchModel' => $searchModel,
                        ])*/
                        ?>
                    </div>

                    <div class="col-sm-6 text-right">
                        <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'studyplan-invoices-grid-pjax']) ?>
                    </div>
                </div>

                <?php
                Pjax::begin([
                    'id' => 'studyplan-invoices-grid-pjax',
                ])
                ?>

                <?= GridView::widget([
                    'id' => 'studyplan-invoices-grid',
                    'pjax' => false, // чтобы работала кнопка Создать новые квитанции
                    'dataProvider' => $dataProvider,
                    // 'filterModel' => $searchModel,
                    'showPageSummary' => false,
                    'showFooter' => true,
                    'bulkActionOptions' => \artsoft\Art::isBackend() ? [
                        'gridId' => 'studyplan-invoices-grid',
                        'actions' => [
//                        Url::to(['bulk-new']) => 'Создать новые квитанции',
                            Url::to(['bulk-delete']) => 'Удалить квитанции',
                            Url::to(['bulk-status', 'status' => StudyplanInvoices::STATUS_WORK]) => 'Перевести в статус "Счет в работе"',
                            Url::to(['bulk-status', 'status' => StudyplanInvoices::STATUS_PAYD]) => 'Перевести в статус "Оплачено"',
                            Url::to(['bulk-status', 'status' => StudyplanInvoices::STATUS_RECEIPT]) => 'Перевести в статус "Поступили средства"',
                            Url::to(['bulk-status', 'status' => StudyplanInvoices::STATUS_ARREARS]) => 'Перевести в статус "Задолженность по оплате"',
//                        Url::to(['bulk-load']) => 'Выгрузить квитанции в Word',
                        ]//Configure here you bulk actions
                    ] : false,
                    /* 'rowOptions' => function($model) {
                         if($model->status == Studyplan::STATUS_INACTIVE) {
                             return ['class' => 'danger'];
                         }
                         return [];
                     },*/
                    'columns' => $columns,
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => 'Ученик/Программа', 'options' => ['colspan' => \artsoft\Art::isBackend() ? 8 : 7, 'class' => 'text-center warning']],
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
<?php


$js = <<<JS
var checkboxes = $("input[name='selection[]']");
   var  btn =  $(".bulk-new");

checkboxes.each(function(idx, itm){
  $(itm).on("change", function() {
      console.log(checkboxes.filter(":checked").length);
    if(checkboxes.filter(":checked").length > 0) {
        btn.removeClass('disabled');        
    }
    else {
        btn.addClass('disabled');  
    }
  });
});

$(document).off('click', '.bulk-new').on('click', '.bulk-new', function (e) {
     e.preventDefault();
$.ajax({
            url: '/admin/invoices/default/bulk-new',
            type: "POST",
            data: $('input[name="selection[]"]:checked').serialize(),
          });

});

JS;
$this->registerJs($js);
