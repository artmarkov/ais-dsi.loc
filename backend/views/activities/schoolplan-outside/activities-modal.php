<?php

use artsoft\helpers\RefBook;
use artsoft\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\activities\Activities */
/* @var $form artsoft\widgets\ActiveForm */


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
                                        'attribute' => 'places',
                                        'value' => function ($model) {
                                            return $model->places;
                                        },
                                    ],
                                    [
                                        'attribute' => 'executors_list',
                                        'value' => function ($model) {
                                            $v = [];
                                            foreach ($model->executors_list as $id) {
                                                $v[] = $id != null ? RefBook::find('teachers_fio')->getValue($id) : null;
                                            }
                                            return implode(', ', $v);
                                        },
                                    ],
                                    'title',
                                    'description:ntext',
                                    'datetime_in:datetime',
                                    'datetime_out:datetime',
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
                        \yii\helpers\Url::to(['schoolplan/default/view', 'id' => $model->id]),
                        [
                            'target' => '_blank',
                            'class' => 'btn btn-info',
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