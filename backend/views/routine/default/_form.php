<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\routine\Routine */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="routine-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'routine-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'cat_id')
                        ->dropDownList(\common\models\routine\RoutineCat::getCatList(), [
                            'prompt' => Yii::t('art/guide', 'Select Cat...')
                        ])->label(Yii::t('art/guide', 'Category'));
                    ?>

                    <?= $form->field($model, 'start_date')->widget(kartik\date\DatePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput(); ?>

                    <?= $form->field($model, 'end_date')->widget(kartik\date\DatePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput() ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

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
