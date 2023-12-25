<?php

use artsoft\grid\GridView;
use artsoft\helpers\RefBook;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\schedule\SubjectScheduleStudyplanView;
use common\models\education\LessonItems;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model_date */
/* @var $modelTeachers */

$this->title = $this->title = 'Табель учета';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'subject',
        'label' => $model['attributes']['subject'],
        'value' => function ($models) {
            return $models['subject'];
        },
        'format' => 'raw',
        'group' => true,
        'footer' => 'Итого: распис./консульт.',
//        'groupFooter' => $editMarks
    ],
    [
        'attribute' => 'sect_name',
        'label' => $model['attributes']['sect_name'],
        'value' => function ($models) {
            return $models['sect_name'];
        },
        'format' => 'raw',
        'group' => true,
        'subGroupOf' => 1,
    ],
    [
        'attribute' => 'subject_type_id',
        'label' => $model['attributes']['subject_type_id'],
        'value' => function ($model) {
            return RefBook::find('subject_type_name')->getValue($model['subject_type_id']);
        },
        'format' => 'raw',
    ],
];
foreach ($model['directions'] as $id => $name) {
    $columns[] = [
        'attribute' => $id['teach'],
        'value' => function ($model) use($id, $modelTeachers) {
        $summ_teach = $model[$id]['teach'] ?? 0;
        $summ_cons = $model[$id]['cons'] ?? 0;
        $label_cons = $model[$id]['title'] ?? '';
            return  $summ_cons != 0 ? $summ_teach . '/' . \artsoft\helpers\Html::a($summ_cons, ['/teachers/default/consult-items', 'id' => $modelTeachers->id],['title' => $label_cons]) : $summ_teach;

        },
        'label' => $model['attributes'][$id]['name'],
        'format' => 'raw',
        'footer' => \common\models\teachers\TeachersTimesheet::getTotal($model['data'], $id),
        'contentOptions' => function ($model) {
            return $model['subject_type_id'] == 1000 ? ['class' => 'success'] : ['class' => 'warning text-right'];

        },
    ];
}


?>
<div class="teachers-progress-index">
    <div class="panel">
        <div class="panel-heading">
            Табель учета: <?php echo RefBook::find('teachers_fullname')->getValue($modelTeachers->id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search-cheet', compact('modelTeachers', 'model_date', 'plan_year')) ?>
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
                'showFooter' => true,
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $model['data'],
                    'sort' => false,
                    'pagination' => false,
                ]),
//                'panel' => [
//                    'heading' => false,
//                    'type' => '',
//                    'footer' => true,
//                ],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учебный предмет/Группа/Ученик', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                            ['content' => 'Часов за период', 'options' => ['colspan' => count($model['directions']), 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
            ]);
            ?>
        </div>
    </div>
</div>
