<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\EntrantGroup */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="entrant-group-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'entrant-group-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка группы
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    // necessary for update action.
                    if (!$model->isNewRecord) {
                        echo Html::activeHiddenInput($model, "comm_id");
                    }
                    ?>
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'prep_flag')->radioList($model->getPrepList(), ['itemOptions' => ['disabled' => $readonly]]) ?>

                    <?= $form->field($model, 'timestamp_in')->widget(DateTimePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true]) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
