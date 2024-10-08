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
$editMarks = function ($models, $key, $index, $widget) {
    $content = [];
    if (SubjectScheduleStudyplanView::getScheduleIsExist($models['subject_sect_studyplan_id'], $models['studyplan_subject_id'])) {
        $content += [2 => Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
            Url::to(['/sect/default/studyplan-progress', 'id' => $models['subject_sect_id'], 'subject_sect_studyplan_id' => $models['subject_sect_studyplan_id'], 'mode' => 'create']),
            [
                'title' => 'Добавить занятие',
                'data-method' => 'post',
                'data-pjax' => '0',
                'class' => 'btn btn-xxs btn-link'

            ]
        )];
    }
    foreach ($models['lesson_timestamp'] as $id => $item) {
        if ($lesson_items_id = LessonItems::isLessonExist($models['subject_sect_studyplan_id'], 0, $item['lesson_date'])) {
            $content += [$id + 3 => Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    Url::to(['/sect/default/studyplan-progress', 'id' => $models['subject_sect_id'], 'objectId' => $lesson_items_id, 'mode' => 'update']), [
                        'title' => Yii::t('art', 'Update'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'class' => 'btn btn-xxs btn-link',
                    ])
                . Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    Url::to(['/sect/default/studyplan-progress', 'id' => $models['subject_sect_id'], 'objectId' => $lesson_items_id, 'mode' => 'delete']), [
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
        }
    }
    return [
        'content' => $content,
        'contentOptions' => [      // content html attributes for each summary cell
            2 => ['class' => 'text-right text-end'],
        ],
        'options' => ['class' => 'info h-25 text-left']
    ];
};
$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'subject_sect_studyplan_id',
        'label' => $models['attributes']['subject_sect_studyplan_id'],
        'value' => function ($models) {
            return $models['sect_name'];
        },
        'format' => 'raw',
        'group' => true,
        'groupFooter' => $editMarks
    ],
    [
        'attribute' => 'student_id',
        'label' => $models['attributes']['student_id'],
        'value' => function ($models) {
            return Html::a($models['student_fio'],
                ['/studyplan/default/schedule-items', 'id' => $models['studyplan_id']],
                [
                    'target' => '_blank',
                    'data-pjax' => '0',
                ],
                [
                    'title' => 'Открыть карточку ученика'
                ]);
        },
        'format' => 'raw',
    ],
];
foreach ($models['lessonDates'] as $id => $name) {
    $columns[] = [
        'attribute' => $name,
        'label' => $models['attributes'][$name],
        'format' => 'raw',
    ];
}

$hints = '<span class="panel-title"><b>Сокращения Вид занятия:</b></span><br/>';
foreach (\common\models\education\LessonTest::getLessonTestHints() as $item => $hint) {
    $hints .= $item . ' - ' . $hint . '; ';
}
$hints .= '<br/><br/>';
$hints .= '<span class="panel-title"><b>Сокращения Оценки:</b></span><br/>';
foreach (\common\models\education\LessonMark::getMarkHints() as $item => $hint) {
    $hints .= $item . ' - ' . $hint . '; ';
}

?>
<div class="studyplan-progress-index">
    <div class="panel">
        <div class="panel-body">
            <?= $this->render('_search-progress', compact('model', 'model_date', 'plan_year')) ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Учебный предмет: <?php echo RefBook::find('sect_name_4')->getValue($model->id);?>
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
                        'pjax' => false,
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $models['data'],
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
                                    ['content' => 'Посещаемость/успеваемость за период', 'options' => ['colspan' => count($models['lessonDates']), 'class' => 'text-center danger']],
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
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::exitButton('/admin/sect/default') ?>
            </div>
        </div>
    </div>
</div>
