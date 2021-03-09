<?php

use artsoft\widgets\ActiveForm;
use common\models\activities\ActivitiesCat;
use kartik\helpers\Html;
use kartik\color\ColorInput;

/* @var $this yii\web\View */
/* @var $model common\models\activities\ActivitiesCat */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="activities-cat-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'activities-cat-form',
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
                        <div class="col-sm-12">

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'color')->widget(ColorInput::classname(), [
                                'options' => ['placeholder' => 'Select color ...'],
                            ]); ?>

                            <?= $form->field($model, 'rendering')->checkbox() ?>

                            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

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
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
