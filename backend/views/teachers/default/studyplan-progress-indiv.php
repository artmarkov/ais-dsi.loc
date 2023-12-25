<?php

use artsoft\grid\GridView;
use artsoft\helpers\RefBook;
use common\models\teachers\Teachers;
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
//echo '<pre>' . print_r($model, true) . '</pre>'; die();

$editMarks = function ($model, $key, $index, $widget) use ($modelTeachers){
    $content = [];
    // if (SubjectScheduleStudyplanView::getScheduleIsExist($model['subject_sect_studyplan_id'], $model['studyplan_subject_id'])) {
    $content += [3 => Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
        \artsoft\Art::isBackend() ? ['/teachers/default/studyplan-progress-indiv', 'id' => $model['teachers_id'], 'subject_key' => base64_encode($model['subject_key'] . '||' . $model['timestamp_in']), 'mode' => 'create'] :
            ['/teachers/studyplan-progress-indiv/create', 'subject_key' => base64_encode($model['subject_key'] . '||' . $model['timestamp_in'])],
        [
            'disabled' => \artsoft\Art::isFrontend() && !Teachers::isOwnTeacher($modelTeachers->id),
            'title' => 'Добавить занятие',
            'data-method' => 'post',
            'data-pjax' => '0',
            'class' => 'btn btn-xxs btn-link'

        ]
    )];
//        }
    foreach ($model['lesson_timestamp'] as $id => $item) {
//        if ($lesson_items_id = LessonItems::isLessonExist($model['subject_sect_studyplan_id'], $model['subject_sect_studyplan_id'] == 0 ? $model['studyplan_subject_id'] : 0, $item['lesson_date'])) {
        $content += [$id + 4 => Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                \artsoft\Art::isBackend() ? ['/teachers/default/studyplan-progress-indiv', 'id' => $model['teachers_id'], 'objectId' => base64_encode($model['subject_key'] . '||' . $item['lesson_date']),'mode' => 'update'] :
                    ['/teachers/studyplan-progress-indiv/update', 'id' => $model['teachers_id'], 'objectId' => base64_encode($model['subject_key'] . '||' . $item['lesson_date'])], [
                    'disabled' => \artsoft\Art::isFrontend() && !Teachers::isOwnTeacher($modelTeachers->id),
                    'title' => Yii::t('art', 'Update'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xxs btn-link',
                ])
            . Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                \artsoft\Art::isBackend() ? ['/teachers/default/studyplan-progress-indiv', 'id' => $model['teachers_id'], 'objectId' => base64_encode($model['subject_key'] . '||' . $item['lesson_date']), 'mode' => 'delete'] :
                    ['/teachers/studyplan-progress-indiv/delete', 'id' => $model['teachers_id'], 'objectId' => base64_encode($model['subject_key'] . '||' . $item['lesson_date'])], [
                    'disabled' => \artsoft\Art::isFrontend() && !Teachers::isOwnTeacher($modelTeachers->id),
                    'title' => Yii::t('art', 'Delete'),
                    'class' => 'btn btn-xxs btn-link',
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'pjax' => '0',
                        'method' => 'post',
                    ],
                ]
            ),
        ];
//        }
    }
    return [
        'content' =>  $content,
        'contentOptions' => [      // content html attributes for each summary cell
            3 => ['class' => 'text-right text-end'],
        ],
        'options' => ['class' => 'info h-25 text-left']
    ];
};
$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'label' => $model['attributes']['studyplan_subject_id'],
        'value' => function ($models) {
            return $models['subject'];
        },
        'format' => 'raw',
        'group' => true,
        'groupFooter' => $editMarks
    ],
    [
        'attribute' => 'subject_sect_studyplan_id',
        'label' => $model['attributes']['subject_sect_studyplan_id'],
        'value' => function ($models) {
            return $models['sect_name'];
        },
        'format' => 'raw',
        'group' => true,
        'subGroupOf' => 1,
        'footer' => 'ИТОГО: ' . $model['dates_load_total'] . ' ак.час.',
    ],
    [
        'attribute' => 'student_id',
        'label' => $model['attributes']['student_id'],
        'value' => function ($model) {
            return Html::a($model['student_fio'],
                \artsoft\Art::isBackend() ? ['/studyplan/default/schedule-items', 'id' => $model['studyplan_id']] : ['/teachers/studyplan/schedule-items', 'id' => $model['studyplan_id']],
                [
                    'target' => '_blank',
//                    'class' => 'btn btn-link',
                ]);
        },
        'format' => 'raw',
    ],
];
foreach ($model['lessonDates'] as $id => $name) {
    $columns[] = [
        'attribute' => $name['date'],
        'label' => $model['attributes'][$name['date']],
        'format' => 'raw',
        'footer' => $name['dates_load'],
    ];
}

$hints = '<h3 class="panel-title">Сокращения:</h3><br/>';
foreach (\common\models\education\LessonMark::getMarkHints() as $item => $hint) {
    $hints .= $item . ' - ' . $hint . '; ';
}

?>
<div class="teachers-progress-index">
    <div class="panel">
        <div class="panel-heading">
            Результаты запроса: <?php echo RefBook::find('teachers_fullname')->getValue($modelTeachers->id); ?>
        </div>
        <div class="panel-body">
            <?= $this->render('_search-progress-indiv', compact('modelTeachers', 'model_date', 'plan_year')) ?>
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
                            ['content' => 'Учебный предмет/Группа/Ученик', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                            ['content' => 'Посещаемость за период', 'options' => ['colspan' => count($model['lessonDates']), 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
            ]);
            ?>
        </div>
    </div>
</div>
