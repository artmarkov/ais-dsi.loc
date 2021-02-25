<?php

use artsoft\widgets\ActiveForm;
use artsoft\queue\models\QueueSchedule;
use artsoft\helpers\Html;
use artsoft\models\User;

/* @var $this yii\web\View */
/* @var $model artsoft\queue\models\QueueSchedule */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="queue-schedule-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'queue-schedule-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'status')->dropDownList(QueueSchedule::getStatusList()) ?>

                    <?= $form->field($model, 'priority')->dropDownList(QueueSchedule::getPriorityList()) ?>

                    <?= $form->field($model, 'class')->textInput(['disabled' => !User::hasPermission('editClassJob')]) ?>

                    <?= $form->field($model, 'cron_expression')->textInput() ?>
                </div>
                <div class="col-md-4">
                    <?= \artsoft\queue\widgets\ExamplesCronWidget::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group">

                <?= Html::a(Yii::t('art', 'Go to list'), ['/queue-schedule/default/index'], ['class' => 'btn btn-default']) ?>
                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                <?php if (!$model->isNewRecord): ?>
                    <?= Html::a(Yii::t('art', 'Delete'),
                        ['/queue-schedule/default/delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]) ?>
                <?php endif; ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
