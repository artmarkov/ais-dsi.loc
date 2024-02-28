<?php

use artsoft\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\activities\search\ActivitiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сводная успеваемость';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
//    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'student_id',
        'label' => $model['attributes']['student_id'],
        'value' => function ($models) {
            return sprintf('#%06d', $models['student_id']);
        },
        'format' => 'raw',
        'group' => true,
    ],
    [
        'attribute' => 'student_fio',
        'label' => $model['attributes']['student_fio'],
        'value' => function ($model) {
            return Html::a($model['student_fio'],
                \artsoft\Art::isBackend() ? ['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id']] : ['/teachers/studyplan/studyplan-progress', 'id' => $model['studyplan_id']],
                [
                    'target' => '_blank',
//                    'class' => 'btn btn-link',
                ]);
        },
        'format' => 'raw',
    ],
    [
        'attribute' => 'education_cat_short_name',
        'label' => $model['attributes']['education_cat_short_name'],
    ],
    [
        'attribute' => 'education_programm_short_name',
        'label' => $model['attributes']['education_programm_short_name'],
    ],
    [
        'attribute' => 'course',
        'label' => $model['attributes']['course'],
    ],
    [
        'attribute' => 'subject_form_name',
        'label' => $model['attributes']['subject_form_name'],
    ],


];
foreach ($model['subjectKeys'] as $item => $val) {
//echo '<pre>' . print_r($model['dataNeeds'], true) . '</pre>'; die();
    $columns[] = [
        'attribute' => $val,
        'label' => $model['attributes'][$val],
        'format' => 'raw',
        'contentOptions' => function ($models) use ($model, $val) {
            return $model['dataNeeds'][$models['studyplan_id']][$val] ? ['class' => 'info', 'style'=>'text-align:center; vertical-align: middle;'] : ['class' => '', 'style'=>'text-align:center; vertical-align: middle;'];

        },
    ];
}

$hints = '<span class="panel-title"><b>Сокращения Оценки:</b></span><br/>';
foreach (\common\models\education\LessonMark::getMarkHints() as $item => $hint) {
    $hints .= $item . ' - ' . $hint . '; ';
}

?>
<div class="activities-index">
    <div class="panel">
        <div class="panel-heading">
        </div>
            <?= $this->render('_search', compact('model_date')) ?>
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
                'showPageSummary' => false,
                'showFooter' => \artsoft\Art::isBackend(),
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $model['data'],
                    'sort' => false,
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
                            ['content' => 'Ученик/Программа/Класс', 'options' => ['colspan' => 6, 'rowspan' => 3, 'class' => 'text-center warning', 'style' => 'vertical-align: middle;']],
                            ['content' => 'Посещаемость/успеваемость', 'options' => ['colspan' => count($model['subjectKeys']), 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ],
                    [
                        'columns' => $model['header'][0],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ],
                     [
                        'columns' => $model['header'][1],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
            ]);
            ?>
        </div>
    </div>
</div>
