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
                ['/teachers/default/portfolio', 'id' => $models['teachers_id']],
                [
                    'target' => '_blank',
//                    'class' => 'btn btn-link',
                ]);
        },
        'format' => 'raw',

    ],
    [
        'attribute' => 'scale',
        'label' => $model['attributes']['scale'],
        'value' => function ($models) {
            return $models['scale'];
        },
        'format' => 'raw',

    ],
];

?>

<div class="execution-schedule-index">
    <div class="panel">
        <div class="panel-heading">
            Контроль выполнения планов и участия в мероприятиях
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
                            // 'footer' => $hints,
                        ],
                        'columns' => $columns,
//                        'beforeHeader' => [
//                            [
//                                'columns' => [
//                                    ['content' => 'Преподаватель', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
//                                    ['content' => 'Шкала выполнения', 'options' => ['colspan' => 1, 'class' => 'text-center warning']],
//                                ],
//                                'options' => ['class' => 'skip-export'] // remove this row from export
//                            ]
//                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


