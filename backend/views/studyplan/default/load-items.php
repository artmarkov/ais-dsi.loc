<?php

use artsoft\helpers\RefBook;
use common\widgets\editable\Editable;
use yii\helpers\Url;
use artsoft\helpers\Html;
use artsoft\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\teachers\search\TeachersLoadStudyplanViewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Teachers Load');
$this->params['breadcrumbs'][] = $this->title;

$JSSubmit = <<<EOF
    function(event, val, form) {
//    console.log(event);
//    console.log(val);
//    console.log(form);
    $.pjax.reload({container: '#teachers-load-grid-pjax', async: true});
    }
EOF;

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
        'value' => function ($model) use ($JSSubmit){
            if($model->subject_sect_studyplan_id == 0) {
                return $model->sect_name ? $model->sect_name : null;
            } else {
                $sectList =  \yii\helpers\ArrayHelper::map((new \yii\db\Query())->select('id, sect_name_1')
                    ->from('subject_sect_view')
                    ->where(['=', 'subject_id', $model->subject_id])
                    ->orderBy('sect_name_1')->all(), 'id', 'sect_name_1');

                return Editable::widget([
                    'buttonsTemplate' => "{reset}{submit}",
                    'name' => 'subject_sect_studyplan_id',
                    'asPopover' => true,
                    'value' => $model->subject_sect_studyplan_id,
                    'header' => '',
                    'displayValueConfig' => $sectList,
                    'format' => Editable::FORMAT_LINK,
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'data' => $sectList,
                    'size' => 'md',
                    'options' => ['class' => 'form-control', 'placeholder' => Yii::t('art', 'Select...')],
                    'formOptions' => [
                        'action' => Url::toRoute(['/sect/default/set-studyplan', 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id, 'studyplan_subject_id' => $model->studyplan_subject_id]),
                    ],
                    'pluginEvents' => [
                        "editableSubmit" => new JsExpression($JSSubmit),
                    ],
                ]);
            }
        },
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
        'attribute' => 'year_time_consult',
        'value' => function ($model) {
            return $model->year_time_consult;
        },
        'group' => true,
        'subGroupOf' => 1,
    ],
    [
        'attribute' => 'direction_id',
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'teachers_id',
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'load_time',
        'value' => function ($model) {
            return $model->load_time === null ? $model->load_time : $model->load_time . ' ' . $model->getItemLoadStudyplanNotice();
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'load_time_consult',
        'value' => function ($model) {
            return $model->load_time_consult . ' ' . $model->getItemLoadStudyplanConsultNotice();
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
                if ($model->subject_sect_studyplan_id == 0) {
                    return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                        Url::to(['/studyplan/default/load-items', 'id' => $model->studyplan_id, 'studyplan_subject_id' => $model->studyplan_subject_id, 'mode' => 'create']), [
                            'title' => Yii::t('art', 'Create'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'disabled' => true
                        ]
                    );
                } else {
                    return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                        Url::to(['/studyplan/default/load-items', 'id' => $model->studyplan_id, 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id, 'mode' => 'create']), [
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
                    Url::to(['/studyplan/default/load-items', 'id' => $model->studyplan_id, 'objectId' => $model->teachers_load_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
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
            'create' => function ($model) {
                return $model->subject_sect_studyplan_id !== null;
            },
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
        <div class="panel-heading">
            Нагрузка: <?= RefBook::find('students_fio')->getValue($model->student_id);?>
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'teachers-load-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'teachers-load-grid-pjax',
            ])
            ?>
            <?= GridView::widget([
                'id' => 'teachers-load-grid',
                'pjax' => false,
                'dataProvider' => $dataProvider,
               // 'filterModel' => $searchModel,
                'showPageSummary' => false,
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет/Группа', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                            ['content' => 'Нагрузка', 'options' => ['colspan' => 7, 'class' => 'text-center info']],
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

