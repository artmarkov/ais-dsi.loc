<?php

use artsoft\grid\GridPageSize;
use artsoft\grid\GridView;
use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use common\models\subjectsect\SubjectSectSchedule;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = Yii::t('art/guide', 'Schedule Items');
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
                        'id' => 'subject-sect-schedule-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
//                        'bulkActionOptions' => [
//                            'gridId' => 'subject-sect-schedule-grid',
//                            'actions' => [Url::to(['bulk-delete']) => 'Delete'] //Configure here you bulk actions
//                        ],
                        'columns' => [
//                            ['class' => 'artsoft\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                            [
                                'attribute' => 'id',
                                'value' => function (SubjectSectSchedule $model) {
                                    return Html::a(sprintf('#%06d', $model->id), ['update', 'id' => $model->id], ['data-pjax' => 0]);
                                },
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'subject_sect_studyplan_id',
                                'class' => 'artsoft\grid\columns\TitleActionColumn',
                                'controller' => 'sect/default/schedule-items',
                                'title' => function (SubjectSectSchedule $model) {
                                    return RefBook::find('sect_name_1')->getValue($model->subject_sect_studyplan_id);
                                },
                                'options' => ['style' => 'width:300px'],
                                'buttonsTemplate' => '{update} {delete}',
                                'buttons' => [
                                    'update' => function ($url, SubjectSectSchedule $model, $key) {
                                        return Html::a(Yii::t('art', 'Edit'),
                                            Url::to(['/sect/default/schedule-items', 'id' => $model->subjectSectStudyplan->subject_sect_id, 'objectId' => $model->id, 'mode' => 'update']), [
                                                'title' => Yii::t('art', 'Edit'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, SubjectSectSchedule $model, $key) {
                                        return Html::a(Yii::t('art', 'Delete'),
                                            Url::to(['/sect/default/schedule-items', 'id' => $model->subjectSectStudyplan->subject_sect_id, 'objectId' => $model->id, 'mode' => 'delete']), [
                                                'title' => Yii::t('art', 'Delete'),
                                                'aria-label' => Yii::t('art', 'Delete'),
                                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                ],
                            ],
                            [
                                'attribute' => 'direction_id',
                                'filter' => \common\models\guidejob\Direction::getDirectionList(),
                                'value' => function (SubjectSectSchedule $model) {
                                    return $model->direction->name;
                                },
                            ],
                            [
                                'attribute' => 'teachers_id',
                                'filter' => RefBook::find('teachers_fio')->getList(),
                                'value' => function (SubjectSectSchedule $model) {
                                    return RefBook::find('teachers_fio')->getValue($model->teachers_id);
                                },
                            ],
                            [
                                'attribute' => 'week_num',
                                'filter' => \artsoft\helpers\ArtHelper::getWeekList(),
                                'value' => function (SubjectSectSchedule $model) {
                                    return $model->week_num != 0 ? \artsoft\helpers\ArtHelper::getWeekList()[$model->week_num] : null;
                                },
                            ],
                            [
                                'attribute' => 'week_day',
                                'filter' => \artsoft\helpers\ArtHelper::getWeekdayList(),
                                'value' => function (SubjectSectSchedule $model) {
                                    return \artsoft\helpers\ArtHelper::getWeekdayList()[$model->week_day];
                                },
                            ],
                            'time_in:time',
                            'time_out:time',
                            [
                                'attribute' => 'auditory_id',
                                'filter' => RefBook::find('auditory_memo_1')->getList(),
                                'options' => ['style' => 'width:300px'],
                                'value' => function (SubjectSectSchedule $model) {
                                    return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
                                },
                            ],
                        ],
                    ]);
                    ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>