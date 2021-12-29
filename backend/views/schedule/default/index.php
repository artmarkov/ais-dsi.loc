<?php

use artsoft\helpers\RefBook;
use yii\helpers\Url;
use yii\widgets\Pjax;
//use artsoft\grid\GridView;
use artsoft\grid\GridQuickLinks;
use common\models\subjectsect\SubjectSectSchedule;
use artsoft\helpers\Html;
use artsoft\grid\GridPageSize;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\subjectsect\search\SubjectSectScheduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('art/guide', 'Subject Sect Schedules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-sect-schedule-index">
    <div class="panel">
        <div class="panel-heading">
            <?= \artsoft\helpers\ButtonHelper::createButton(); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            /* Uncomment this to activate GridQuickLinks */
                            /* echo GridQuickLinks::widget([
                                'model' => SubjectSectSchedule::className(),
                                'searchModel' => $searchModel,
                            ])*/
                            ?>
                        </div>

                        <div class="col-sm-6 text-right">
                            <?= GridPageSize::widget(['pjaxId' => 'subject-sect-schedule-grid-pjax']) ?>
                        </div>
                    </div>

                    <?php
                    Pjax::begin([
                        'id' => 'subject-sect-schedule-grid-pjax',
                    ])
                    ?>

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'kartik\grid\SerialColumn'],
                            [
                                'attribute' => 'subject_sect_id',
                                'width' => '310px',
                                'value' => function ($model, $key, $index, $widget) {
                                    return $model->subject_sect_id;
                                },

                                'group' => true,  // enable grouping
                            ],
                            [
                                'attribute' => 'studyplan_subject_list',
                                'width' => '310px',
                                'value' => function ($model, $key, $index, $widget) {
                                    return $model->studyplan_subject_list;
                                },

                                'group' => true,  // enable grouping
                            ],
                            [
                                'attribute' => 'subject_sect_studyplan_id',
                                'width' => '310px',
                                'value' => function ($model, $key, $index, $widget) {
                                    return $model->subject_sect_studyplan_id;
                                },

                                'group' => true,  // enable grouping
                            ],
                            [
                                'attribute' => 'direction_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => \common\models\guidejob\Direction::getDirectionList(),
                                'value' => function ($model, $key, $index, $widget) {
                                    return $model->direction->name;
                                },
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                                'filterInputOptions' => ['placeholder' => Yii::t('art', 'Select...')],
                                'group' => true,  // enable grouping
                            ],
                            [
                                'attribute' => 'teachers_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('teachers_fio')->getList(),
                                'value' => function ($model) {
                                    return RefBook::find('teachers_fio')->getValue($model->teachers_id);
                                },

                                'group' => true,  // enable grouping
                            ],
                            'week_time',

//                            'plan_year',
//                            'subject_sect_schedule_id',
                            [
                                'attribute' => 'week_num',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => \artsoft\helpers\ArtHelper::getWeekList(),
                                'value' => function ($model) {
                                    return $model->week_num != 0 ? \artsoft\helpers\ArtHelper::getWeekList()[$model->week_num] : null;
                                },
                            ],
                            [
                                'attribute' => 'week_day',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => \artsoft\helpers\ArtHelper::getWeekdayList(),
                                'value' => function ($model) {
                                    return isset($model->week_day) ? \artsoft\helpers\ArtHelper::getWeekdayList()[$model->week_day] : null;
                                },
                            ],

                            'time_in:time',
                            'time_out:time',
                            [
                                'attribute' => 'auditory_id',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => RefBook::find('auditory_memo_1')->getList(),
                                'options' => ['style' => 'width:300px'],
                                'value' => function ($model) {
                                    return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
                                },
                            ],
//                            'description',
                        ],
                        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
//    'beforeHeader'=>[
//        [
//            'columns'=>[
//                ['content'=>'Header Before 1', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 2', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 3', 'options'=>['colspan'=>3, 'class'=>'text-center warning']],
//            ],
//            'options'=>['class'=>'skip-export'] // remove this row from export
//        ]
//    ],
                        'toolbar' => [
                            ['content' =>
                            // Html::button('&lt;i class="glyphicon glyphicon-plus">&lt;/i>', ['type'=>'button', 'title'=>Yii::t('art', 'Add Book'), 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
                                Html::a('Reset', ['/schedule'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('art', 'Reset Grid')])
                            ],
                            '{export}',
                            '{toggleData}'
                        ],
                        'pjax' => true,
                        'bordered' => true,
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => true,
                        'hover' => false,
                        'floatHeader' => false,
//    'floatHeaderOptions' => ['top' => $scrollingTop],
                        'showPageSummary' => false,
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


