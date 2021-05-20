<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\parents\Parents */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $userCommon common\models\user\UserCommon */
?>

<div class="parents-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'parents-form',
//        'validateOnBlur' => false,
    ])

    ?>

    <div class="panel">
        <div class="panel-heading">
            Информация о родителе (официальном представителе)
        </div>
        <div class="panel-body">

            <?= $this->render('/user/_form', ['form' => $form, 'model' => $userCommon, 'readonly' => false]) ?>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Документ
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'sert_name')->dropDownList(\common\models\parents\Parents::PARENT_DOC, [
                                'disabled' => false,
                                'options' => [
                                    'password' => ['selected' => true]
                                ]
                            ]) ?>
                            <?= $form->field($model, 'sert_series')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'sert_num')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'sert_organ')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'sert_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => false]); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::saveButton();?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

