<?php

use yii\helpers\Html;
use artsoft\widgets\ActiveForm;
use kartik\color\ColorInput;

/* @var $this yii\web\View */
/* @var $model common\models\routine\RoutineCat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="routine-cat-form">
    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Html::encode($this->title) ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php $form = ActiveForm::begin(); ?>

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?=
                            $form->field($model, 'color')->widget(ColorInput::classname(), [
                                'options' => ['placeholder' => 'Select color ...'],
                            ]);
                            ?>
                            <?= $form->field($model->loadDefaultValues(), 'plan_flag')->dropDownList(\common\models\routine\RoutineCat::getPlanFlagList()) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
