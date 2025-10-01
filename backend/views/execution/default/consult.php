<?php

/* @var $this yii\web\View */

use artsoft\grid\GridView;
use artsoft\helpers\RefBook;
use yii\helpers\Html;
use yii\widgets\Pjax;
$teachers_list = RefBook::find('teachers_fio')->getList();

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'teachers_id',
        'label' => $model['attributes']['teachers_id'],
        'value' => function ($models) use ($teachers_list){
            return Html::a($teachers_list[$models['teachers_id']],
                ['/teachers/default/consult-items', 'id' => $models['teachers_id']],
                [
                    'target' => '_blank',
//                    'class' => 'btn btn-link',
                ]);
        },
        'format' => 'raw',

    ],
    [
        'attribute' => 'scale_0',
        'label' => $model['attributes']['scale_0'],
        'value' => function ($models) {
            return $models['scale_0'];
        },
        'format' => 'raw',

    ],
    [
        'attribute' => 'scale_1',
        'label' => $model['attributes']['scale_1'],
        'value' => function ($models) {
            return $models['scale_1'];
        },
        'format' => 'raw',

    ],
    [
        'attribute' => 'confirm_status',
        'label' => $model['attributes']['confirm_status'],
        'value' => function ($models) {
            return \common\models\execution\ExecutionSchedule::getStatusLabel($models['confirm_status']);
        },
        'contentOptions' => ['style'=>"text-align:center; vertical-align: middle;"],
        'format' => 'raw',

    ],
    [
        'attribute' => 'teachers_sign',
        'label' => $model['attributes']['teachers_sign'],
        'value' => function ($models) use ($teachers_list){
            return $teachers_list[$models['teachers_sign']] ?? '';
        },
        'format' => 'raw',

    ],
];

?>

<div class="execution-schedule-index">
    <div class="panel">
        <div class="panel-heading">
            Расписания консультаций на подписи
        </div>
        <div class="panel-body">
            <?= $this->render('_search', compact('model_date')) ?>
            <div class="panel panel-default">
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
                            <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'studyplan-progress-grid-pjax']) ?>
                        </div>
                    </div>
                    <?php
                    Pjax::begin([
                        'id' => 'studyplan-progress-grid-pjax',
                    ])
                    ?>
                    <?php
                    echo GridView::widget([
                        'id' => 'studyplan-progress-grid',
                        'pjax' => false,
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $model['data'],
                            'sort' => false,
                            'pagination' => false,
                        ]),
                        'panel' => [
                            'heading' => false,
                            'type' => '',
                            'footer' => \common\models\execution\ExecutionScheduleConsult::getCheckLabelHints(),
                        ],
                        'columns' => $columns,
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Преподаватель', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
                                    ['content' => 'Шкала выполнения', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
                                    ['content' => 'Подпись', 'options' => ['colspan' => 3, 'class' => 'text-center danger']],
                                ],
                                'options' => ['class' => 'skip-export'] // remove this row from export
                            ]
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


