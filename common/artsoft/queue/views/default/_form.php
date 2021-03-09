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

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-8">

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
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
