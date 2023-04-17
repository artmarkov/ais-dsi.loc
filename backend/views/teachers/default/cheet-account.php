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

$this->title = $this->title = Yii::t('art/guide', 'Indiv Progress');
$this->params['breadcrumbs'][] = $this->title;
//echo '<pre>' . print_r($modelTeachers, true) . '</pre>';
//echo '<pre>' . print_r($model_date, true) . '</pre>'; die();

//$editMarks = function ($model, $key, $index, $widget) {
//    $content = [];
//   // if (SubjectScheduleStudyplanView::getScheduleIsExist($model['subject_sect_studyplan_id'], $model['studyplan_subject_id'])) {
//            $content += [3 => Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
//                Url::to(['/teachers/default/studyplan-progress-indiv', 'id' => $model['teachers_id'], 'subject_key' => base64_encode($model['subject_key'] . '||' . $model['timestamp_in']), 'mode' => 'create']),
//                [
//                    'title' => 'Добавить занятие',
//                    'data-method' => 'post',
//                    'data-pjax' => '0',
//                    'class' => 'btn btn-xxs btn-link'
//
//                ]
//            )];
////        }
//    foreach ($model['lesson_timestamp'] as $id => $item) {
////        if ($lesson_items_id = LessonItems::isLessonExist($model['subject_sect_studyplan_id'], $model['subject_sect_studyplan_id'] == 0 ? $model['studyplan_subject_id'] : 0, $item['lesson_date'])) {
//            $content += [$id + 4 => Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
//                    Url::to(['/teachers/default/studyplan-progress-indiv', 'id' => $model['teachers_id'], 'objectId' => base64_encode($model['subject_key'] . '||' . $item['lesson_date']),'mode' => 'update']), [
//                        'title' => Yii::t('art', 'Update'),
//                        'data-method' => 'post',
//                        'data-pjax' => '0',
//                        'class' => 'btn btn-xxs btn-link',
//                    ])
//                . Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
//                    Url::to(['/teachers/default/studyplan-progress-indiv', 'id' => $model['teachers_id'], 'objectId' => base64_encode($model['subject_key'] . '||' . $item['lesson_date']), 'mode' => 'delete']), [
//                        'title' => Yii::t('art', 'Delete'),
//                        'class' => 'btn btn-xxs btn-link',
//                        'data' => [
//                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
//                            'pjax' => '0',
//                            'method' => 'post',
//                        ],
//                    ]
//                ),
//            ];
////        }
//    }
//    return [
//        'content' => $content,
//        'contentOptions' => [      // content html attributes for each summary cell
//            3 => ['class' => 'text-right text-end'],
//        ],
//        'options' => ['class' => 'info h-25 text-left']
//    ];
//};
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
        'attribute' => $id,
        'label' => $model['attributes'][$id],
        'format' => 'raw',
        'pageSummary' => true,
        'pageSummaryFunc' => GridView::F_SUM
    ];
}


?>
<div class="teachers-progress-index">
    <div class="panel">
        <div class="panel-heading">
            Табель учета: <?php echo RefBook::find('teachers_fio')->getValue($modelTeachers->id); ?>
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
                'showPageSummary' => true,
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
                            ['content' => 'Учебный предмет/Группа/Ученик', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                            ['content' => 'Часов в неделю', 'options' => ['colspan' => count($model['directions']) + 1, 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
            ]);
            ?>
        </div>
    </div>
</div>
