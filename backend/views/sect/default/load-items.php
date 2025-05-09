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
$typeList = RefBook::find('subject_type_name')->getList();
$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'sect_name',
        'width' => '310px',
        /*'value' => function ($model, $key, $index, $widget) {
            return $model->sect_name ? $model->sect_name . $model->getSectNotice() : null;
        },*/
        'value' => function ($model)  {
            return \artsoft\Art::isBackend() && $model->sect_name ? Html::a($model->sect_name,
                ['/sect/default/studyplan-progress', 'id' => $model->subject_sect_id, 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id],
                [
//                    'target' => '_blank',
                    'data-pjax' => '0',
//                    'class' => 'btn btn-info',
                ]) . $model->getSectNotice() : ($model->sect_name ? $model->sect_name . $model->getSectNotice() : null);
        },
        'format' => 'raw',
        'group' => true,  // enable grouping
    ],
    [
        'attribute' => 'subject_type_id',
        'value' => function ($model) use ($typeList){
            return Editable::widget([
                'buttonsTemplate' => "{reset}{submit}",
                'name' => 'subject_type_id',
                'asPopover' => true,
                'value' => $model->subject_type_id,
                'header' => '',
                'displayValueConfig' => $typeList,
                'format' => Editable::FORMAT_LINK,
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => $typeList,
                'size' => 'md',
                'options' => ['class' => 'form-control', 'placeholder' => Yii::t('art', 'Select...')],
                'formOptions' => [
                    'action' => Url::toRoute(['/sect/default/set-type', 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id]),
                ],
            ]);
        },
        'format' => 'raw',
        'group' => true,
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'week_time',
        'value' => function ($model) {
            return $model->week_time;
        },
        'group' => true,
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'year_time_consult',
        'value' => function ($model) {
            return $model->year_time_consult;
        },
        'group' => true,
        'subGroupOf' => 1
    ],

    [
        'attribute' => 'direction_id',
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
        'group' => true,
        'subGroupOf' => 4

    ],
    [
        'attribute' => 'teachers_id',
        'value' => function ($model) {
            return \artsoft\Art::isBackend() ?  Html::a(RefBook::find('teachers_fio')->getValue($model->teachers_id),
                ['/teachers/default/load-items', 'id' => $model->teachers_id],
                [
                    'target' => '_blank',
                    'data-pjax' => '0',
//                    'class' => 'btn btn-info',
                ]) : RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 5
    ],
    [
        'attribute' => 'load_time',
        'value' => function ($model) {
            return $model->load_time . $model->getItemLoadNotice();
        },
        'format' => 'raw',
    ],
     [
        'attribute' => 'load_time_consult',
        'value' => function ($model) {
            return $model->load_time_consult . ' ' . $model->getItemLoadConsultNotice();
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
                    ['/sect/default/load-items', 'id' => $model->subject_sect_id, 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id, 'mode' => 'create'], [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            },
            'update' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/sect/default/load-items', 'id' => $model->subject_sect_id, 'objectId' => $model->teachers_load_id, 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Edit'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'delete' => function ($key, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/sect/default/load-items', 'id' => $model->subject_sect_id, 'objectId' => $model->teachers_load_id, 'mode' => 'delete'], [
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
        ]
    ],
];
?>
<div class="sect-load-index">
    <div class="panel">
        <div class="panel-heading">
            Нагрузка:  <?php echo RefBook::find('sect_name_4')->getValue($model->id);?>
            <?= $this->render('_search', compact('model_date')) ?>
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'sect-load-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'sect-load-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'sect-load-grid',
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
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
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::exitButton('/admin/sect/default') ?>
            </div>
        </div>
    </div>
</div>


