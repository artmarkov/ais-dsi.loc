<?php

use artsoft\grid\GridView;
use artsoft\helpers\RefBook;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\schedule\SubjectScheduleStudyplanView;
use common\models\education\LessonItems;
use yii\widgets\Pjax;

$this->title = Yii::t('art/guide', 'Group Progress');
$this->params['breadcrumbs'][] = $this->title;
//echo '<pre>' . print_r($model, true) . '</pre>'; die();
$editMarks = function ($model, $key, $index, $widget) {
    $content = [];
    if (SubjectScheduleStudyplanView::getScheduleIsExist($model['subject_sect_studyplan_id'], $model['studyplan_subject_id'])) {
        $content += [2 => Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
            Url::to(['/sect/default/studyplan-progress', 'id' => $model['subject_sect_id'], 'subject_sect_studyplan_id' => $model['subject_sect_studyplan_id'], 'mode' => 'create']),
            [
                'title' => 'Добавить занятие',
                'data-method' => 'post',
                'data-pjax' => '0',
                'class' => 'btn btn-xs btn-link'

            ]
        )];
    }
    foreach ($model['lesson_timestamp'] as $id => $item) {
        if ($lesson_items_id = LessonItems::isLessonExist($model['subject_sect_studyplan_id'], 0, $item['lesson_date'])) {
            $content += [$id + 3 => Html::a('<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
                    Url::to(['/sect/default/studyplan-progress', 'id' => $model['subject_sect_id'], 'objectId' => $lesson_items_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Update'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'class' => 'btn btn-xs btn-link',
                    ])
                . Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i>',
                    Url::to(['/sect/default/studyplan-progress', 'id' => $model['subject_sect_id'], 'objectId' => $lesson_items_id, 'mode' => 'delete']), [
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
        'attribute' => 'subject_sect_studyplan_id',
        'label' => $model['attributes']['subject_sect_studyplan_id'],
        'value' => function ($model) {
            return RefBook::find('sect_name_2')->getValue($model['subject_sect_studyplan_id'] ?? null);
        },
        'format' => 'raw',
        'group' => true,
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
<div class="studyplan-progress-index">
    <div class="panel">
        <div class="panel-body">
            <?= $this->render('@app/views/studyplan/lesson-items/_search', compact('model_date')) ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Результаты запроса
                </div>
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
                    <?= GridView::widget([
                        'id' => 'studyplan-progress-grid',
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $model['data'],
                            'sort' => false,
                            'pagination' => false,
                        ]),
                        'filterModel' => null,
                        'columns' => $columns,
                        'panel' => [
                            'heading' => false,
                            'type' => '',
                            'footer' => $hints,
                        ],
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Группа/Ученик', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                                    ['content' => 'Посещаемость за период', 'options' => ['colspan' => count($model['lessonDates']), 'class' => 'text-center danger']],
                                ],
                                'options' => ['class' => 'skip-export'] // remove this row from export
                            ]
                        ],
                    ]);
                    ?>
                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
