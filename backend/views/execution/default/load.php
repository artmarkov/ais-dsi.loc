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
                ['/teachers/default/load-items', 'id' => $models['teachers_id']],
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

<div class="execution-load-index">
    <div class="panel">
        <div class="panel-heading">
            Контроль заполнения нагрузки преподавателя
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
                            <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'studyplan-load-grid-pjax']) ?>
                        </div>
                    </div>
                    <?php
                    Pjax::begin([
                        'id' => 'studyplan-load-grid-pjax',
                    ])
                    ?>
                    <?php
                    echo GridView::widget([
                        'id' => 'studyplan-load-grid',
                        'pjax' => false,
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $model['data'],
                            'sort' => false,
                            'pagination' => false,
                        ]),
                        'panel' => [
                            'heading' => false,
                            'type' => '',
                            'footer' => \common\models\execution\ExecutionLoad::getCheckLabelHints(),
                        ],
                        'columns' => $columns,
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


