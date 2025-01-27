<?php

use artsoft\helpers\RefBook;
use artsoft\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\activities\Activities */
/* @var $form artsoft\widgets\ActiveForm */


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
                                            if ($model->executor_name != null) {
                                                return $model->executor_name;
                                            } else {
                                                $v = [];
                                                foreach (explode(',', $model->executors_list) as $id) {
                                                    $v[] = $id != null ? RefBook::find('teachers_fio')->getValue($id) : null;
                                                }
                                                return implode(', ', $v);
                                            }
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
                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::closeButton('cancel-activities'); ?>
                    <?= Html::a('<i class="fa fa-calendar-check-o" aria-hidden="true"></i> Открыть в новом окне',
                        $url,
                        [
                            'target' => '_blank',
                            'class' => 'btn btn-info',
                            'visible' => true
                        ]); ?>
                </div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
$('.cancel-activities').on('click', function (e) {
         e.preventDefault();
         closeModal();
});

function closeModal() {
    $('#activities-modal').modal('hide');
}
JS;

$this->registerJs($js);
?>