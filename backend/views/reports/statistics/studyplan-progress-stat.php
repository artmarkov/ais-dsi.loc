<?php

use artsoft\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model_date */
/* @var $data */

$this->title = 'Статистика по успеваемости';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
//    ['class' => 'kartik\grid\SerialColumn'],
//    [
//        'attribute' => 'id',
//        'label' => $model['attributes']['id'],
//        'group' => true,
//    ],
    [
        'attribute' => 'name',
        'label' => $model['attributes']['name'],
        'group' => true,
    ],

    [
        'attribute' => 'mark',
        'label' => $model['attributes']['mark'],
        'format' => 'raw',
        'contentOptions' => function ($model) {
            return $model['mark'] == 'Нет оценки' ? ['class' => 'danger', 'style' => 'text-align:center; vertical-align: middle;'] : ['class' => '', 'style' => 'text-align:center; vertical-align: middle;'];

        },
    ],
    [
        'attribute' => 'count_mark',
        'label' => $model['attributes']['count_mark'],
        'format' => 'raw',
        'contentOptions' => function ($model) {
            return $model['mark'] == 'Нет оценки' ? ['class' => 'danger', 'style' => 'text-align:center; vertical-align: middle;'] : ['class' => '', 'style' => 'text-align:center; vertical-align: middle;'];

        },
    ],

];
foreach ($model['course_list'] as $item => $val) {
    $columns[] = [
        'attribute' => $val,
        'label' => $model['attributes'][$val],
        'format' => 'raw',
        'contentOptions' => function ($model) {
            return $model['mark'] == 'Нет оценки' ? ['class' => 'danger', 'style' => 'text-align:center; vertical-align: middle;'] : ['class' => '', 'style' => 'text-align:center; vertical-align: middle;'];

        },
    ];
}

$hints = '<span class="panel-title"><b>Итого по запросу: ' . array_sum([$model['all_summ']['5'],$model['all_summ']['4'],$model['all_summ']['3'],$model['all_summ']['НА'],$model['all_summ']['0']]).'</b></span><br/>';
$hints .= 'Отлично: ' . $model['all_summ']['5'];
$hints .= '<br/>';
$hints .= 'Хорошо: ' . $model['all_summ']['4'];
$hints .= '<br/>';
$hints .= 'Удовлетворительно: ' . $model['all_summ']['3'];
$hints .= '<br/>';
$hints .= 'Неудовлетворительно/Не аттестован: ' . $model['all_summ']['НА'];
$hints .= '<br/>';
$hints .= 'Нет оценки: ' . $model['all_summ']['0'];
?>
<div class="studyplan-progress-stat-index">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <?= $this->render('_progress_stat_search', compact('model_date')) ?>
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'studyplan-progress-stat-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'studyplan-progress-stat-grid-pjax',
            ])
            ?>
            <?php
            echo GridView::widget([
                'id' => 'studyplan-progress-stat-grid',
                'pjax' => false,
                'showPageSummary' => false,
                'showFooter' => \artsoft\Art::isBackend(),
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $model['data'],
                    'sort' => [
                        'attributes' => array_keys($model['attributes'])
                    ],
                    'pagination' => false,
                ]),
                'panel' => [
                    'heading' => false,
                    'type' => '',
                    'footer' => $hints,
                ],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Программа/Оценка', 'options' => ['colspan' => 3, 'rowspan' => 2, 'class' => 'text-center warning', 'style' => 'vertical-align: middle;']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ],
                    [
                        'columns' => [
                            ['content' => 'По классам', 'options' => ['colspan' => count($model['course_list']), 'class' => 'text-center info']]
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
</div>
