<?php

use artsoft\helpers\RefBook;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\schedule\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


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
        'width' => '310px',
        'value' => function ($model, $key, $index, $widget) {
            return $model->sect_name ? $model->sect_name /*. $model->getSectNotice()*/ : null;
        },
        'label' =>  Yii::t('art/guide', 'Sect').'/'.Yii::t('art/student', 'Student'),
        'format' => 'raw',
        'group' => true,  // enable grouping
        'subGroupOf' => 1,
    ],
//    [
//        'attribute' => 'week_time',
//        'value' => function ($model) {
//            return $model->week_time;
//        },
//        'group' => true,
//        'subGroupOf' => 2,
//    ],
    [
        'attribute' => 'direction_id',
        'value' => function ($model, $key, $index, $widget) {
            return $model->direction ? $model->direction->name : null;
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 2
    ],
    [
        'attribute' => 'teachers_id',
        'value' => function ($model) {
            return RefBook::find('teachers_fio')->getValue($model->teachers_id);
        },
        'group' => true,  // enable grouping
        'subGroupOf' => 3
    ],
//    [
//        'attribute' => 'load_time',
//        'value' => function ($model) {
//            return $model->load_time . ' ' . $model->getItemLoadNotice();
//        },
//        'format' => 'raw',
//    ],
    [
        'attribute' => 'scheduleDisplay',
        'width' => '300px',
        'value' => function ($model) {
            return $model->getScheduleDisplay();
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'auditory_id',
        'width' => '300px',
        'value' => function ($model) {
            return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
        },
    ],
];
?>
<div class="subject-schedule-index">
    <div class="panel">
        <div class="panel-body">
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => false,
                'tableOptions' => ['class' => 'table-condensed'],
//                        'showPageSummary' => true,
//                'pjax' => true,
                'hover' => true,
                'panel' => [
                    'heading' => 'Элементы расписания',
                    'type' => 'default',
                    'after' => '',
                ],
                'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет/Группа/Ученик', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                            ['content' => 'Расписание занятий', 'options' => ['colspan' => 4, 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
                'toolbar' => [],
            ]);
            ?>
        </div>
    </div>
</div>

