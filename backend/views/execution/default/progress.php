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
                ['/teachers/default/schedule-items', 'id' => $models['teachers_id']],
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
];

?>

<div class="execution-thematic-index">
    <div class="panel">
        <div class="panel-heading">
            Контроль заполнения журналов успеваемости
        </div>
        <div class="panel-body">
            <?= $this->render('_search-progress', compact('model_date')) ?>
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
                            'footer' => \common\models\execution\ExecutionProgress::getCheckLabelHints(),
                        ],
                        'columns' => $columns,
//                        'beforeHeader' => [
//                            [
//                                'columns' => [
//                                    ['content' => 'Преподаватель', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
//                                    ['content' => \artsoft\helpers\ArtHelper::getHalfYearValue(1), 'options' => ['colspan' => 2, 'class' => 'text-center info']],
//                                    ['content' => \artsoft\helpers\ArtHelper::getHalfYearValue(2), 'options' => ['colspan' => 2, 'class' => 'text-center info']],
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


