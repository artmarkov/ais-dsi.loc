<?php

use artsoft\grid\GridView;
use artsoft\helpers\RefBook;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model_date */
/* @var $teachers_id */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/studyplan', 'Students and groups');
$this->params['breadcrumbs'][] = $this->title;
//echo '<pre>' . print_r($model['columns'], true) . '</pre>'; die();

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'value' => function ($models) {
            return $models->subject;
        },
        'format' => 'raw',
        'group' => true,
    ],
    [
        'attribute' => 'subject_sect_studyplan_id',
        'value' => function ($models) {
            return $models->sect_name;
        },
        'format' => 'raw',
        'group' => true,
        'subGroupOf' => 1,
    ],
    [
        'attribute' => 'student_id',
        'value' => function ($model) {
            return $model->student_fio;
        },
        'format' => 'raw',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
        'width' => '90px',
        'template' => '{view}',
        'buttons' => [
            'view' => function ($key, $model) {
                return \artsoft\helpers\Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>',
                    \artsoft\Art::isBackend() ? ['/studyplan/default/load-items', 'id' => $model->studyplan_id] : ['/teachers/studyplan/view', 'id' => $model->studyplan_id], [
                        'title' => Yii::t('art', 'View'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'target' => '_blank',
                    ]
                );
            },

        ],
    ],
];

?>
<div class="teachers-studyplan-list-index">
    <div class="panel">
        <div class="panel-heading">
           Список учеников и групп: <?php echo RefBook::find('teachers_fullname')->getValue($teachers_id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date', 'teachers_id')) ?>

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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'studyplan-progress-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'teachers-studyplan-list-pjax',
            ])
            ?>
            <?= GridView::widget([
                'id' => 'portfolio-grid',
                'dataProvider' => $dataProvider,
                'columns' => $columns,
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
