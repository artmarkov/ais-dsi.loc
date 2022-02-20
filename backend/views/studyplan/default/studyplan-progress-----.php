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
/* @var $searchModel common\models\subjectsect\search\SubjectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Schedule');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studyplan-progress-index">
    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
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
                            <?= GridPageSize::widget(['pjaxId' => 'studyplan-progress-grid-pjax']) ?>
                        </div>
                    </div>
                    <?php
                    Pjax::begin([
                        'id' => 'studyplan-progress-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'kartik\grid\SerialColumn'],
                            [
                                'attribute' => 'studyplan_subject_id',
                                'value' => function ($model) {
                                    return RefBook::find('subject_memo_2')->getValue($model->studyplan_subject_id ?? null);
                                },
                                'format' => 'raw',
                                'group' => true,
                            ],
                            [
                                'attribute' => 'subject_vid_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('subject_vid_name')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('subject_vid_name_dev')->getValue($model->subject_vid_id);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'format' => 'raw',
                                'group' => true,
                                'subGroupOf' => 1
                            ],
                            [
                                'attribute' => 'subject_sect_studyplan_id',
                                'width' => '310px',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('sect_name_1')->getList(),
                                'value' => function ($model, $key, $index, $widget) {
                                    return RefBook::find('sect_name_1')->getValue($model->subject_sect_studyplan_id);
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                                'subGroupOf' => 1
                            ],
                           'lesson_qty',
                           'current_qty',
                           'absence_qty',
                           'current_avg_mark',
                           'middle_avg_mark',
                           'finish_avg_mark',

                            [
                                'class' => 'kartik\grid\ActionColumn',
                                'vAlign' => \kartik\grid\GridView::ALIGN_MIDDLE,
                                'width' => '90px',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function ($key, $model) {
                                        if($model->subject_sect_studyplan_id == null) {
                                            return Html::a('<i class="fa fa-eye" aria-hidden="true"></i>',
                                                Url::to(['/studyplan/default/studyplan-progress', 'id' => $model->studyplan_id, 'studyplan_subject_id' => $model->studyplan_subject_id, 'mode' => 'update']), [
                                                    'title' => Yii::t('art', 'View'),
                                                    'data-method' => 'post',
                                                    'data-pjax' => '0',
                                                    'disabled' => true
                                                ]
                                            );
                                        }
                                        else {
                                            return Html::a('<i class="fa fa-eye" aria-hidden="true"></i>',
                                                Url::to(['/studyplan/default/studyplan-progress', 'id' => $model->studyplan_id, 'subject_sect_studyplan_id' => $model->subject_sect_studyplan_id, 'mode' => 'update']), [
                                                    'title' => Yii::t('art', 'View'),
                                                    'data-method' => 'post',
                                                    'data-pjax' => '0',
                                                    'disabled' => true
                                                ]
                                            );
                                        }
                                    },
                                ],
                                'visibleButtons' => [
                                    'view' => function () {
                                        return true;
                                    }
                                ]
                            ],
                        ],
                        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
                        'beforeHeader' => [
                            [
                                'columns' => [
                                    ['content' => 'Дисциплина', 'options' => ['colspan' => 4, 'class' => 'text-center warning']],
                                    ['content' => 'Статистика обучения', 'options' => ['colspan' => 7, 'class' => 'text-center info']],
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
                                    Url::to(['/studyplan/default/studyplan-progress', 'id' => $id]), [
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

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

