<?php

use artsoft\widgets\ActiveForm;
use common\models\own\Invoices;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\own\Invoices */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="invoices-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'invoices-form',
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/own/default/history', 'id' => $model->id]); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'recipient')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'inn')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'kpp')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'payment_account')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'corr_account')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'personal_account')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'bik')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'oktmo')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'kbk')->textInput(['maxlength' => true]) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
