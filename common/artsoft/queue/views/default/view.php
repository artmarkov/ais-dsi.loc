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

    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
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
            <div class="form-group">
                <?= Html::a(Yii::t('art', 'Go to list'), ['/queue-schedule/default/index'], ['class' => 'btn btn-default']) ?>
                <?= Html::a(Yii::t('art', 'Edit'), ['/queue-schedule/default/update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
                <?= Html::a(Yii::t('art', 'Delete'), ['/queue-schedule/default/delete', 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-danger',
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
    </div>

</div>
