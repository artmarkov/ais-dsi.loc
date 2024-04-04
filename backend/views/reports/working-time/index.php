<?php

use artsoft\grid\GridView;
use common\models\service\WorkingTimeLog;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\activities\search\ActivitiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Журнал посещаемости';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="working-time-index">
    <div class="panel">
        <div class="panel-heading">
        </div>
        <?= $this->render('_search', compact('model_date')) ?>
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
                    <?= \artsoft\grid\GridPageSize::widget(['pjaxId' => 'working-time-grid-pjax']) ?>
                </div>
            </div>
            <?php
            Pjax::begin([
                'id' => 'working-time-grid-pjax',
            ])
            ?>
            <?php
            echo GridView::widget([
                'id' => 'working-time-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => false,
//                'formatter'=> ['class'=> 'yii\i18n\Formatter', 'nullDisplay' => '(not set)'],
                'columns' => [
                    [
                        'attribute' => 'user_common_id',
                        'label' => '#',
                        'value' => function ($models) {
                            return sprintf('#%06d', $models['user_common_id']);
                        },
                        'options' => ['style' => 'width:10px'],
                    ],
                    [
                        'attribute' => 'user_common_id',
                        'value' => function (WorkingTimeLog $model) {
                            return $model->userCommon->getFullName();
                        },
                    ],
                    [
                        'attribute' => 'timestamp_activities_in',
                        'value' => function (WorkingTimeLog $model) {
                            return Yii::$app->formatter->asTime($model->timestamp_activities_in);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'timestamp_activities_out',
                        'value' => function (WorkingTimeLog $model) {
                            return Yii::$app->formatter->asTime($model->timestamp_activities_out);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'timestamp_work_in',
                        'value' => function (WorkingTimeLog $model) {
                            return Yii::$app->formatter->asTime($model->timestamp_work_in);
                        },
                        'contentOptions' => function (WorkingTimeLog $model) {
                            if (!$model->timestamp_work_in) {
                                return [];
                            } elseif ($model->timestamp_work_in > $model->timestamp_activities_in) {
                                return ['class' => 'danger'];
                            } elseif (($model->timestamp_activities_in - $model->timestamp_work_in) < WorkingTimeLog::TIME_RESERV ) {
                                return ['class' => 'warning'];
                            }
                            return ['class' => 'success'];
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'timestamp_work_out',
                        'value' => function (WorkingTimeLog $model) {
                            return Yii::$app->formatter->asTime($model->timestamp_work_out);
                        },
                        'contentOptions' => function (WorkingTimeLog $model) {
                            if (!$model->timestamp_work_out) {
                                return [];
                            } elseif ($model->timestamp_work_out < $model->timestamp_activities_out) {
                                return ['class' => 'danger'];
                            }
                            elseif (($model->timestamp_work_out - $model->timestamp_activities_out) > WorkingTimeLog::TIME_EXIT ) {
                                return ['class' => 'warning'];
                            }
                            return ['class' => 'success'];
                        },
                        'format' => 'raw',
                    ],
                ],
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Учетная запись', 'options' => ['colspan' => 2, 'class' => 'text-center info']],
                            ['content' => 'Согласно расписанию занятий', 'options' => ['colspan' => 2, 'class' => 'text-center success', 'style' => 'vertical-align: middle;']],
                            ['content' => 'Фактическое время посещения', 'options' => ['colspan' => 2, 'class' => 'text-center warning']],
                        ],
//                        'options' => ['class' => 'skip-export'] // remove this row from export
                    ],
                ],
            ]);
            ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
