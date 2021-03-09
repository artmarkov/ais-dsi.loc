<?php

use yii\widgets\DetailView;
use artsoft\helpers\Html;
use artsoft\queue\models\QueueSchedule;

/* @var $this yii\web\View */
/* @var $model artsoft\queue\models\QueueSchedule */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/queue', 'Queue Schedules'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="queue-schedule-view">
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'title',
                            'content:ntext',
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function (QueueSchedule $model) {
                                    return $model->getStatusList()[$model->status];
                                },
                            ],
                            [
                                'attribute' => 'priority',
                                'format' => 'raw',
                                'value' => function (QueueSchedule $model) {
                                    return $model->getPriorityList()[$model->priority];
                                },
                            ],
                            'cron_expression',
                            [
                                'attribute' => 'nextDates',
                                'value' => function (QueueSchedule $model) {
                                    $string = '';
                                    foreach ($model->getNextDates() as $date) {
                                        $string .= $date->format('d-m-Y H:i l') . PHP_EOL;
                                        $string .= '<br \>';
                                    }
                                    return $string;
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'createdDatetime',
                                'label' => $model->attributeLabels()['created_at'],
                            ],
                            [
                                'attribute' => 'updatedDatetime',
                                'label' => $model->attributeLabels()['updated_at'],
                            ],
                            [
                                'attribute' => 'createdBy',
                                'value' => function (QueueSchedule $model) {
                                    return $model->createdBy->username;
                                },
                                'label' => $model->attributeLabels()['created_by'],
                            ],
                            [
                                'attribute' => 'updatedBy',
                                'value' => function (QueueSchedule $model) {
                                    return $model->updatedBy->username;
                                },
                                'label' => $model->attributeLabels()['updated_by'],
                            ],
                        ],
                    ]);
                    ?>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::viewButtons($model, '/queue-schedule/default/index', ['/queue-schedule/default/update', 'id' => $model->id], ['/queue-schedule/default/delete', 'id' => $model->id]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
