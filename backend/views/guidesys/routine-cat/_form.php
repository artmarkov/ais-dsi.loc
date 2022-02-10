<?php

use yii\helpers\Html;
use artsoft\widgets\ActiveForm;
use kartik\color\ColorInput;

/* @var $this yii\web\View */
/* @var $model common\models\routine\RoutineCat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="routine-cat-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'routine-cat-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?=
                    $form->field($model, 'color')->widget(ColorInput::classname(), [
                        'options' => ['placeholder' => 'Select color ...'],
                    ]);
                    ?>
                    <?= $form->field($model, 'vacation_flag')->checkbox(['disabled' => false]) ?>
                    <?= $form->field($model, 'dayoff_flag')->checkbox(['disabled' => false]) ?>
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
    <?php ActiveForm::end(); ?>
</div>
