<?php

use artsoft\helpers\RefBook;
use common\models\studyplan\Studyplan;
use yii\helpers\Url;
use yii\widgets\Pjax;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use kartik\grid\GridView;
use common\models\subjectsect\SubjectScheduleView;

/* @var $this yii\web\View */
/* @var $data */
/* @var $model_date */

$this->title = Yii::t('art/guide', 'Studyplan Progress');
$this->params['breadcrumbs'][] = $this->title;
//echo '<pre>' . print_r($data['data'], true) . '</pre>'; die();

$columns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'studyplan_subject_id',
        'label' => $data['attributes']['studyplan_subject_id'],
        'value' => function ($data) {
            return RefBook::find('subject_memo_2')->getValue($data['studyplan_subject_id'] ?? null);
        },
        'format' => 'raw',
        'group' => true,
//        'contentOptions' => ['class'=>'block pin'],
    ],
     [
        'attribute' => 'subject_sect_studyplan_id',
        'label' => $data['attributes']['subject_sect_studyplan_id'],
        'value' => function ($data) {
            return RefBook::find('sect_name_1')->getValue($data['subject_sect_studyplan_id'] ?? null);
        },
        'format' => 'raw',
        'group' => true,
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'subject_vid_id',
        'label' => $data['attributes']['subject_vid_id'],
        'value' => function ($data) {
            return RefBook::find('subject_vid_name_dev')->getValue($data['subject_vid_id'] ?? null);
        },
        'format' => 'raw',
        'group' => true,
        'subGroupOf' => 1
    ],
    [
        'attribute' => 'lesson_qty',
        'label' => $data['attributes']['lesson_qty'],
    ],
    [
        'attribute' => 'current_qty',
        'label' => $data['attributes']['current_qty'],
    ],
    [
        'attribute' => 'absence_qty',
        'label' => $data['attributes']['absence_qty'],
    ],
    [
        'attribute' => 'current_avg_mark',
        'label' => $data['attributes']['current_avg_mark'],
    ],
    [
        'attribute' => 'middle_avg_mark',
        'label' => $data['attributes']['middle_avg_mark'],
    ],
    [
        'attribute' => 'finish_avg_mark',
        'label' => $data['attributes']['finish_avg_mark'],
    ],
//    'buttonsTemplate' => '{details} {bar}',
//    'buttons' => [
//        'details' => function ($url, $data, $key) {
//            return Html::a('Показатели',
//                Url::to(['efficiency/default/details', 'id' => $data['id'], 'timestamp_in' => $data['date_in'], 'timestamp_out' => $data['date_out']]), [
//                    'data-method' => 'post',
//                    'data-pjax' => '0',
//                ]);
//        },
//        'bar' => function ($url, $data, $key) {
//            return Html::a('График',
//                Url::to(['efficiency/default/user-bar', 'id' => $data['id'], 'timestamp_in' => $data['date_in'], 'timestamp_out' => $data['date_out']]), [
//                    'data-method' => 'post',
//                    'data-pjax' => '0',
//                ]);
//        }
//    ],
//    'format' => 'raw',
//    'options' => ['style' => 'width:250px'],
//    'headerOptions' => ['class' => "grid"],
];
foreach ($data['lessonDates'] as $id => $name) {
    $columns[] = [
        'attribute' => $name,
        'label' => $data['attributes'][$name],
    ];
}
//echo '<pre>' . print_r($data['data'], true) . '</pre>'; die();
?>

<div class="studyplan-progress-index">
    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $this->render('_progress_search', compact('model_date')) ?>

                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Результаты запроса
                        </div>
                        <div class="panel-body">
                            <?= GridView::widget([
                                'id' => 'studyplan-progress',
                                'dataProvider' => new \yii\data\ArrayDataProvider([
                                    'allModels' => $data['data'],
                                    'sort' => [
                                        'attributes' => array_keys($data['attributes'])
                                    ],
                                    'pagination' => false,
                                ]),
                                'columns' => $columns,
                                'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
                                'beforeHeader' => [
                                    [
                                        'columns' => [
                                            ['content' => 'Дисциплина', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                                            ['content' => 'Статистика обучения', 'options' => ['colspan' => 6, 'class' => 'text-center info']],
                                            ['content' => 'Посещаемость за период', 'options' => ['colspan' => count($data['lessonDates']), 'class' => 'text-center danger']],
                                        ],
                                        'options' => ['class' => 'skip-export'] // remove this row from export
                                    ]
                                ],
                                'export' => [
                                    'fontAwesome' => true
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
                                            Url::to(['/studyplan/default/studyplan-progress', 'id' => $data['studyplan_id']]), [
                                                'title' => 'Очистить',
                                                'data-pjax' => '0',
                                                'class' => 'btn btn-default'
                                            ]
                                        ),
                                    ],
                                    '{export}',
                                    '{toggleData}'
                                ],
                                'pjax' => true,
                                'bordered' => true,
                                'striped' => true,
                                'condensed' => true,
                                'responsive' => false,
                                'hover' => false,
                                'floatHeader' => false,
//    'floatHeaderOptions' => ['top' => $scrollingTop],
                                'showPageSummary' => false,
                                //'layout' => '{items}',
                                'panel' => [
                                    'type' => GridView::TYPE_DEFAULT
                                ],
                            ]);
                            ?>
                        </div>
                    </div>

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