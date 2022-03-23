<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use artsoft\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model */
/* @var $model_date */

$this->title = Yii::t('art/guide', 'Studyplan Progress');
$this->params['breadcrumbs'][] = $this->title;
//echo '<pre>' . print_r($model['data'], true) . '</pre>'; die();

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
//        'contentOptions' => ['class'=>'block pin'],
    ],
    [
        'attribute' => 'subject_sect_studyplan_id',
        'label' => $model['attributes']['subject_sect_studyplan_id'],
        'value' => function ($model) {
            return RefBook::find('sect_name_1')->getValue($model['subject_sect_studyplan_id'] ?? null);
        },
        'format' => 'raw',
        'group' => true,
        'subGroupOf' => 1
    ],
//    [
//        'attribute' => 'subject_vid_id',
//        'label' => $model['attributes']['subject_vid_id'],
//        'value' => function ($model) {
//            return RefBook::find('subject_vid_name_dev')->getValue($model['subject_vid_id'] ?? null);
//        },
//        'format' => 'raw',
//        'group' => true,
//        'subGroupOf' => 1
//    ],

];
foreach ($model['lessonDates'] as $id => $name) {
    $columns[] = [
        'attribute' => $name,
        'label' => $model['attributes'][$name],
        'format' => 'raw',
    ];
}
$columns[] = [

    'class' => 'kartik\grid\ActionColumn',
    'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
    'width' => '90px',
    'header' => '',
    'template' => '{create}',
    'buttons' => [
        'create' => function ($key, $model) {
            if ($model['subject_sect_studyplan_id'] == null) {
                return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                    Url::to(['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'studyplan_subject_id' => $model['studyplan_subject_id'], 'mode' => 'create']), [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            } else {
                return Html::a('<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
                    Url::to(['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'subject_sect_studyplan_id' => $model['subject_sect_studyplan_id'], 'mode' => 'create']), [
                        'title' => Yii::t('art', 'Create'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'disabled' => true
                    ]
                );
            }

        },

    ],
    'visibleButtons' => [
        'create' => function ($model) {
            return \common\models\subjectsect\SubjectScheduleTeachersView::getScheduleIsExist($model['subject_sect_studyplan_id'], $model['studyplan_subject_id']);
        }
    ]
];

$hints = '<h3 class="panel-title">Сокращения:</h3><br/>';
foreach (\common\models\education\LessonMark::getMarkHints() as $item => $hint) {
    $hints .= $item . ' - ' . $hint . '; ';
}
?>

    <div class="studyplan-progress-index">
        <div class="panel">
            <div class="panel-body">
                <div class="panel panel-default">
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
                                        ['content' => 'Дисциплина/Группа', 'options' => ['colspan' => 3, 'class' => 'text-center warning']],
                                        ['content' => 'Посещаемость за период', 'options' => ['colspan' => count($model['lessonDates']) + 1, 'class' => 'text-center danger']],
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
                                        Url::to(['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id']]), [
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

<?php
$css = <<<CSS
.block {
  position: relative;
  padding-left: 5rem;
  margin: 0 auto;
  width: 50%;
}
.pin {
  position: absolute;
  display: block;
  /*top: 0;*/
  /*left: 0;*/
  /*bottom: 0;*/
  /*width: 3rem;*/
  /*height: 100%;*/
  /*background: rgba(221, 221, 221, 0.5);*/
  writing-mode: vertical-lr;
  display: flex;
}
.pin span {
  margin: auto;
  transform: rotate(180deg);
}

CSS;

$this->registerCss($css);
?>