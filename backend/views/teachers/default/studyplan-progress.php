<?php

use kartik\grid\GridView;
use artsoft\helpers\RefBook;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\schedule\SubjectScheduleStudyplanView;
use common\models\education\LessonItems;

$this->title = $this->title = Yii::t('art/guide', 'Journal Progress');
$this->params['breadcrumbs'][] = $this->title;
//echo '<pre>' . print_r($model, true) . '</pre>'; die();
$editMarks = function ($model, $key, $index, $widget) {
    $content = [];
    if (SubjectScheduleStudyplanView::getScheduleIsExist($model['subject_sect_studyplan_id'], $model['studyplan_subject_id'])) {
        if ($model['subject_sect_studyplan_id'] != 0) {
            $content += [3 => Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                Url::to(['/teachers/default/studyplan-progress', 'id' => $model['teachers_id'], 'subject_sect_studyplan_id' => $model['subject_sect_studyplan_id'], 'mode' => 'create']),
                [
                    'title' => 'Добавить занятие',
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-link'

                ]
            )];
        } else {
            $content += [3 => Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                Url::to(['/teachers/default/studyplan-progress', 'id' => $model['teachers_id'], 'studyplan_subject_id' => $model['studyplan_subject_id'], 'mode' => 'create']),
                [
                    'title' => 'Добавить занятие',
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-link'

                ]
            )];
        }
    }
    foreach ($model['lesson_timestamp'] as $id => $item) {
        if ($lesson_items_id = LessonItems::isLessonExist($model['subject_sect_studyplan_id'], $model['subject_sect_studyplan_id'] == 0 ? $model['studyplan_subject_id'] : 0, $item['lesson_date'])) {
            $content += [$id + 4 => Html::a('<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
                    Url::to(['/teachers/default/studyplan-progress', 'id' => $model['teachers_id'], 'objectId' => $lesson_items_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Update'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'class' => 'btn btn-xs btn-link',
                    ])
                . Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                    Url::to(['/teachers/default/studyplan-progress', 'id' => $model['teachers_id'], 'objectId' => $lesson_items_id, 'mode' => 'delete']), [
                        'title' => Yii::t('art', 'Delete'),
                        'class' => 'btn btn-xs btn-link',
                        'data' => [
                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'pjax' => '0',
                            'method' => 'post',
                        ],
                    ]
                ),
            ];
        }
    }
    return [
        'content' => $content,
        'contentOptions' => [      // content html attributes for each summary cell
            2 => ['class' => 'text-right text-end'],
        ],
        'options' => ['class' => 'info h-25 text-center']
    ];
};
$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'label' => $model['attributes']['studyplan_subject_id'],
        'value' => function ($model) {
            return RefBook::find('subject_memo_1')->getValue($model['studyplan_subject_id'] ?? null);
        },
        'format' => 'raw',
        'group' => true,
    ],
    [
        'attribute' => 'subject_sect_studyplan_id',
        'label' => $model['attributes']['subject_sect_studyplan_id'],
        'value' => function ($model) {
            return RefBook::find('sect_name_3')->getValue($model['subject_sect_studyplan_id'] ?? null);
        },
        'format' => 'raw',
        'group' => true,
        'subGroupOf' => 1,
        'groupFooter' => $editMarks
    ],
    [
        'attribute' => 'student_id',
        'label' => $model['attributes']['student_id'],
        'value' => function ($model) {
            return RefBook::find('students_fio')->getValue($model['student_id'] ?? null);
        },
        'format' => 'raw',
    ],
];
foreach ($model['lessonDates'] as $id => $name) {
    $columns[] = [
        'attribute' => $name,
        'label' => $model['attributes'][$name],
        'format' => 'raw',
    ];
}

$hints = '<h3 class="panel-title">Сокращения:</h3><br/>';
foreach (\common\models\education\LessonMark::getMarkHints() as $item => $hint) {
    $hints .= $item . ' - ' . $hint . '; ';
}

?>
<div class="teachers-progress-index">
    <div class="panel">
        <div class="panel-body">
            <?= $this->render('@app/views/studyplan/lesson-items/_search', compact('model_date')) ?>
            <?php
            echo GridView::widget([
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $model['data'],
                    'sort' => false,
                    'pagination' => false,
                ]),
                'tableOptions' => ['class' => 'table-condensed'],
                'filterModel' => null,
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
//                        'showPageSummary' => true,
                'pjax' => true,
                'hover' => true,
                'panel' => [
                    'heading' => 'Результаты запроса',
                    'type' => 'default',
                    'after' => '',
                    'footer' => $hints,
                ],
                'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
                'columns' => $columns,
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Дисциплина/Группа/Ученик', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                            ['content' => 'Посещаемость за период', 'options' => ['colspan' => count($model['lessonDates']), 'class' => 'text-center danger']],
                        ],
                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ]
                ],
                'exportConfig' => [
                    'html' => [],
                    'csv' => [],
                    'txt' => [],
                    'xls' => [],
                ],
                'toolbar' => [
                    '{export}',
                    '{toggleData}'
                ],
            ]);
            ?>
        </div>
    </div>
</div>