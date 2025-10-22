<?php

use artsoft\helpers\RefBook;
use common\models\education\LessonItems;
use common\models\schedule\SubjectScheduleStudyplanView;
use yii\helpers\Url;
use artsoft\helpers\Html;
use artsoft\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model */
/* @var $model_date */
/* @var $modelStudent */

$this->title = Yii::t('art/guide', 'Studyplan Progress');
$this->params['breadcrumbs'][] = $this->title;
$columnsHeader = [];
foreach ($model['columns'] as $my => $qty) {
    $columnsHeader[] = ['content' => $my, 'options' => ['colspan' => $qty, 'class' => 'text-center']];
}
//echo '<pre>' . print_r($columns, true) . '</pre>'; die();
$editMarks = function ($model, $key, $index, $widget) {
    $content = [];
    if (SubjectScheduleStudyplanView::getScheduleIsExist($model['subject_sect_studyplan_id'], $model['studyplan_subject_id'])) {
        if ($model['subject_sect_studyplan_id'] != 0) {
            $content += [2 => \yii\helpers\Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                ['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'subject_sect_studyplan_id' => $model['subject_sect_studyplan_id'], 'mode' => 'create'],
                [
                    'title' => 'Добавить занятие',
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xxs btn-link'

                ]
            )];
        } else {
            $content += [2 => Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
                ['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'studyplan_subject_id' => $model['studyplan_subject_id'], 'mode' => 'create'],
                [
                    'title' => 'Добавить занятие',
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xxs btn-link'

                ]
            )];
        }
    }
    foreach ($model['lesson_timestamp'] as $id => $item) {
        if ($lesson_items_id = LessonItems::isLessonExist($model['subject_sect_studyplan_id'], $model['subject_sect_studyplan_id'] == 0 ? $model['studyplan_subject_id'] : 0, $item['lesson_date'])) {
            $content += [$id + 3 => ($model['subject_sect_studyplan_id'] == 0 ? Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'objectId' => $lesson_items_id, 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Update'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'class' => 'btn btn-xxs btn-link',
                    ]) : Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'subject_sect_studyplan_id' => $model['subject_sect_studyplan_id'], 'objectId' => $lesson_items_id, 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Update'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'class' => 'btn btn-xxs btn-link',
                    ])) .
                ($model['subject_sect_studyplan_id'] == 0 ? Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'objectId' => $lesson_items_id, 'mode' => 'delete'], [
                        'title' => Yii::t('art', 'Delete'),
                        'class' => 'btn btn-xxs btn-link',
                        'data' => [
                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'pjax' => '0',
                            'method' => 'post',
                        ],
                    ]
                ) : null),
            ];
        }
    }
    foreach ($model['certif'] as $id => $item) {
        if ($lesson_items_id = LessonItems::isLessonCertifExist($model['subject_sect_studyplan_id'], $model['subject_sect_studyplan_id'] == 0 ? $model['studyplan_subject_id'] : 0, $item['lesson_test_id'])) {
            $content += [$id + 3 + count($model['lesson_timestamp']) => ($model['subject_sect_studyplan_id'] == 0 ? Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'objectId' => $lesson_items_id, 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Update'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'class' => 'btn btn-xxs btn-link',
                    ]) : Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                    ['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'subject_sect_studyplan_id' => $model['subject_sect_studyplan_id'], 'objectId' => $lesson_items_id, 'mode' => 'update'], [
                        'title' => Yii::t('art', 'Update'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'class' => 'btn btn-xxs btn-link',
                    ])) .
                ($model['subject_sect_studyplan_id'] == 0 ? Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                    ['/studyplan/default/studyplan-progress', 'id' => $model['studyplan_id'], 'objectId' => $lesson_items_id, 'mode' => 'delete'], [
                        'title' => Yii::t('art', 'Delete'),
                        'class' => 'btn btn-xxs btn-link',
                        'data' => [
                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'pjax' => '0',
                            'method' => 'post',
                        ],
                    ]
                ) : null),
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
        'attribute' => 'studyplan_subject_id',
        'label' => $model['attributes']['studyplan_subject_id'],
        'value' => function ($model) {
            return $model['subject'];
        },
        'format' => 'raw',
        'group' => true,
        'groupFooter' => \artsoft\Art::isBackend() ? $editMarks : '',
    ],
    [
        'attribute' => 'subject_sect_studyplan_id',
        'label' => $model['attributes']['subject_sect_studyplan_id'],
        'value' => function ($model) {
            return $model['sect_name'];
        },
        'format' => 'raw',
    ],
];
foreach ($model['lessonDates'] as $id => $name) {
    $columns[] = [
        'attribute' => $name,
        'label' => $model['attributes'][$name],
        'format' => 'raw',
//        'headerOptions' => ['style' => 'height: 50px;'],
//        'contentOptions' => ['style'=>'padding:0px 0px 0px 30px;vertical-align: middle;'],
    ];
}
foreach ($model['certif'] as $id => $name) {
    $columns[] = [
        'attribute' => $name,
        'label' => $model['attributes'][$name],
        'format' => 'raw',
//        'headerOptions' => ['style' => 'height: 50px;'],
        'contentOptions' => ['style' => 'background-color: #ebebeb;'],
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
            <div class="panel-heading">
                Дневник успеваемости: <?= RefBook::find('students_fullname')->getValue($modelStudent->student_id); ?>
                <?= $modelStudent->getProgrammName() . ' - ' . $modelStudent->course . ' класс.'; ?>
            </div>
            <div class="panel-body">
                <?= $this->render('_search-progress', compact('model_date')) ?>
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
                                ['content' => 'Учебный предмет/Группа', 'options' => ['colspan' => 3, 'rowspan' => 2, 'class' => 'text-center warning', 'style' => 'vertical-align: middle;']],
                                ['content' => 'Посещаемость/успеваемость за период', 'options' => ['colspan' => count($model['lessonDates']), 'class' => 'text-center danger']],
                                ['content' => 'Аттестация', 'options' => ['colspan' => count($model['certif']), 'class' => 'text-center info']],
                            ],
                            'options' => ['class' => 'skip-export'] // remove this row from export
                        ],
                        [
                            'columns' => $columnsHeader,
                            'options' => ['class' => 'skip-export'] // remove this row from export
                        ]
                    ],
                ]);
                ?>
                <?php Pjax::end() ?>
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

\yii\bootstrap\Modal::begin([
    'header' => '<h4 class="lte-hide-title page-title">Карточка занятия</h4>',
    'size' => 'modal-md',
    'id' => 'progress-modal',
]);
\yii\bootstrap\Modal::end();
?>
<?php
$js = <<<JS
$('#progress').on('click', function (e) {
         e.preventDefault();
         var id = $(this).attr('value');
        //console.log(id);
        $.ajax({
            url: 'init-progress-modal',
            type: 'POST',
            data: {
                id: id, 
            },
            success: function (res) {
                console.log(res);
            showProgress(res);
            },
            error: function () {
                alert('Error!!!');
            }
        });
});
function showProgress(res) {
    $('#progress-modal .modal-body').html(res);
    $('#progress-modal').modal();
}
JS;

$this->registerJs($js);
?>
