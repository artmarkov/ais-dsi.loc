<?php

use artsoft\grid\GridView;
use artsoft\helpers\RefBook;
use yii\widgets\DetailView;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activities\Activities */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/calendar', 'Activities'), 'url' => ['activities/default/index']];
$this->params['breadcrumbs'][] = $this->title;
switch ($model->resource) {
    case 'schoolplan':
        $url = \yii\helpers\Url::to(['schoolplan/default/view', 'id' => $model->id]);
        break;
    case 'consult_schedule':
        $url = \yii\helpers\Url::to(['teachers/default/consult-items', 'id' => $model->executors_list, 'objectId' => $model->id, 'mode' => 'update']);
        break;
    case 'activities_over':
        $url = \yii\helpers\Url::to(['activities/activities-over/view', 'id' => $model->id]);
        break;
    case 'subject_schedule':
        $url = \yii\helpers\Url::to(['teachers/default/schedule-items', 'id' => $model->executors_list, 'objectId' => $model->id, 'mode' => 'update']);
        break;
}
?>
<div class="activities-view">
    <div class="panel">
        <div class="panel-heading">
            Карточка мероприятия
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">

                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'attribute' => 'category_id',
                                    'value' => function ($model) {
                                        return \common\models\activities\ActivitiesCat::getCatValue($model->category_id);
                                    },
                                    'label' => Yii::t('art/guide', 'Category'),
                                ],
                                [
                                    'attribute' => 'auditory_id',
                                    'value' => function ($model) {
                                        return RefBook::find('auditory_memo_1')->getValue($model->auditory_id);
                                    },
                                ],
                                [
                                    'attribute' => 'executors_list',
                                    'value' => function ($model) {
                                        $v = [];
                                        foreach (explode(',', $model->executors_list) as $id) {
                                            $v[] = $id != null ? RefBook::find('teachers_fio')->getValue($id) : null;
                                        }
                                        return implode(', ', $v);
                                    },
                                ],
                                'title',
                                'description:ntext',
                                'start_time:datetime',
                                'end_time:datetime',
                            ],
                        ])
                        ?>

                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= Html::a('<i class="fa fa-calendar-check-o" aria-hidden="true"></i> Открыть в новом окне',
                            $url,
                            [
                                'target' => '_blank',
                                'class' => 'btn btn-info',
                            ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
