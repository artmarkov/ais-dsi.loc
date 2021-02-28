<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\user\UserCommon */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="parents-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'parents-form',
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

                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($model, 'gender')->dropDownList(artsoft\models\User::getGenderList()) ?>

                            <?php if ($model->birth_timestamp) $model->birth_timestamp = date("d-m-Y", (integer)mktime(0, 0, 0, date("m", $model->birth_timestamp), date("d", $model->birth_timestamp), date("Y", $model->birth_timestamp))); ?>

                            <?= $form->field($model, 'birth_timestamp')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput() ?>

                            <?= $form->field($model, 'snils')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput() ?>

                            <?= $form->field($model, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>

                            <?= $form->field($model, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/parent/default/index'], ['class' => 'btn btn-default']) ?>
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'),
                                ['/parent/default/delete', 'id' => $model->id], [
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
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
