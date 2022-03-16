<?php

use kartik\grid\GridView;
use artsoft\helpers\RefBook;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\subjectsect\SubjectScheduleTeachersView;

$this->title = Yii::t('art/guide', 'Group Progress');
$this->params['breadcrumbs'][] = $this->title;
$editMarks = function ($model, $key, $index, $widget) {
//    echo '<pre>' . print_r($model['dates'], true) . '</pre>'; die();
    $content = [];
    if(SubjectScheduleTeachersView::getScheduleIsExist($model['subject_sect_studyplan_id'], $model['studyplan_subject_id'])) {
        $content += [2 => Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
            Url::to(['/sect/default/studyplan-progress', 'id' => $model['subject_sect_id'], 'studyplan_subject_id' => $model['studyplan_subject_id'], 'mode' => 'create']), [
                'title' => 'Добавить занятие',
                'data-method' => 'post',
                'data-pjax' => '0',
                // 'disabled' => true
            ]
        )];
    }
    foreach ($model['dates'] as  $id => $item) {

        $content += [ $id + 3 => Html::a( '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
            Url::to(['/sect/default/studyplan-progress', 'id' => $model['subject_sect_id'], 'subject_sect_studyplan_id' => $model['subject_sect_studyplan_id'], 'timestamp' => $item['lesson_date'], 'mode' => 'update']), [
                'title' => Yii::t('art', 'Update'),
                'data-method' => 'post',
                'data-pjax' => '0',
                // 'disabled' => true
            ]
        ),];
    }
//    echo '<pre>' . print_r($content, true) . '</pre>'; die();
    return [
       // 'mergeColumns' => [[1, 12]],
        'content' => $content,
//        'contentFormats' => [      // content reformatting for each summary cell
//            1 => ['format' => 'text'],
//        ],
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
        'label' => $data['attributes']['subject_sect_studyplan_id'],
        'value' => function ($data) {
            return RefBook::find('sect_name_2')->getValue($data['subject_sect_studyplan_id'] ?? null);
        },
        'format' => 'raw',
        'group' => true,
        'groupFooter' => $editMarks
    ],
    [
        'attribute' => 'student_id',
        'label' => $data['attributes']['student_id'],
        'value' => function ($data) {
            return RefBook::find('students_fio')->getValue($data['student_id'] ?? null);
        },
        'format' => 'raw',

    ],

];
foreach ($data['lessonDates'] as $id => $name) {
    $columns[] = [
        'attribute' => $name,
        'label' => $data['attributes'][$name],
        'format' => 'raw',
    ];
}

//$columns[] = [
//
//    'class' => 'kartik\grid\ActionColumn',
//    'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
//    'width' => '90px',
//    'header' => 'Добавить урок',
//    'template' => '{create}',
//    'buttons' => [
//        'create' => function ($key, $data) {
//            return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
//                Url::to(['/sect/default/studyplan-progress', 'id' => $data['subject_sect_id'], 'studyplan_subject_id' => $data['studyplan_subject_id'], 'mode' => 'create']), [
//                    'title' => Yii::t('art', 'Create'),
//                    'data-method' => 'post',
//                    'data-pjax' => '0',
//                    'disabled' => true
//                ]
//            );
//
//        },
//    ],
//    'visibleButtons' => [
//        'create' => function ($data) {
//            return \common\models\subjectsect\SubjectScheduleTeachersView::getScheduleIsExist($data['subject_sect_studyplan_id'], $data['studyplan_subject_id']);
//        }
//    ]
//];
//echo '<pre>' . print_r($data['data'], true) . '</pre>'; die();

$hints = '<h3 class="panel-title">Сокращения:</h3><br/>';
foreach (RefBook::find('lesson_test_hint')->getList() as $item => $hint) {
    $hints .= $item . ' - ' . $hint . '; ';
}


?>
<div class="studyplan-progress-index">
    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $this->render('_progress_search', compact('model_date')) ?>
                    <?php
                    echo GridView::widget([
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $data['data'],
                            'sort' => [
                                'attributes' => array_keys($data['attributes'])
                            ],
                            'pagination' => false,
                        ]),
                        'tableOptions' => ['class' => 'table-condensed'],
                        'filterModel' => null,
                        //'showPageSummary' => true,
                        'pjax' => true,
                        'hover' => true,
                        // 'panel' => ['type' => 'default', 'heading' => 'Результаты запроса'],
                        'panel' => [
                            'heading' => 'Результаты запроса',
                            'type' => 'default',
                           // 'before' => Html::a('<i class="fa fa-plus"></i> Добавить', ['create'], ['class' => 'btn btn-success']),
                            'after' => '',
                            'footer' => $hints,
                        ],
                        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
                        'columns' => $columns,
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Группа/Ученик', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                                    ['content' => 'Посещаемость за период', 'options' => ['colspan' => count($data['lessonDates']), 'class' => 'text-center danger']],
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
                            [
                                'content' => Html::a('Очистить',
                                    Url::to(['/sect/default/studyplan-progress', 'id' => $data['subject_sect_id']]), [
                                        'title' => 'Очистить',
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-default'
                                    ]
                                ),
                            ],
                            '{export}',
                            '{toggleData}'
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
