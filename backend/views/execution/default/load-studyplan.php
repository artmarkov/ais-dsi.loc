<?php

/* @var $this yii\web\View */

use artsoft\grid\GridView;
use artsoft\helpers\RefBook;
use yii\helpers\Html;
use yii\widgets\Pjax;

//$studyplan_list = $models->getStudyplan();
$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'student_fio',
//        'filter' => $studyplan_list,
        'label' => $model['attributes']['student_fio'],
        'value' => function ($models) {
            return Html::a($models['student_fio'] ? $models['student_fio'] : $models['studyplan_id'],
                ['/studyplan/default/load-items', 'id' => $models['studyplan_id']],
                [
                    'target' => '_blank',
//                    'class' => 'btn btn-link',
                ]);
        },
        'format' => 'raw',

    ],
    [
        'attribute' => 'education_programm_name',
        'label' => $model['attributes']['education_programm_name'],
        'value' => function ($models) {
            return $models['education_programm_name'];
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

<div class="execution-load-studyplan-index">
    <div class="panel">
        <div class="panel-heading">
            Контроль заполнения нагрузки индивидуальных планов
        </div>
        <div class="panel-body">
            <?= $this->render('_search-load', compact('model_date')) ?>
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
                            <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'studyplan-load-studyplan-grid-pjax']) ?>
                        </div>
                    </div>
                    <?php
                    Pjax::begin([
                        'id' => 'studyplan-load-grid-pjax',
                    ])
                    ?>
                    <?php
                    echo GridView::widget([
                        'id' => 'studyplan-load-studyplan-grid',
                        'pjax' => false,
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $model['data'],
                            'sort' => [
                                'attributes' => ['student_fio', 'education_programm_name'],
                                'defaultOrder' => [
                                    'student_fio' => SORT_ASC,
                                ],
                            ],
                            'pagination' => [
                                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
                            ],
                        ]),
                        'panel' => [
                            'heading' => false,
                            'type' => '',
                            'footer' => \common\models\execution\ExecutionLoadStudyplan::getCheckLabelHints(),
                        ],
                        'columns' => $columns,
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


