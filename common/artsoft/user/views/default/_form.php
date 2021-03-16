<?php

use artsoft\helpers\Html;
use common\models\user\User;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\ArtHelper;
use yii\widgets\MaskedInput;
use kartik\date\DatePicker;

/**
 * @var yii\web\View $this
 * @var common\models\user\User $model
 * @var artsoft\widgets\ActiveForm $form
 */
?>

<div class="user-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'user',
        'validateOnBlur' => false,
    ]);
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
                            <?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
                            <?php if ($model->isNewRecord): ?>
                                <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
                                <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
                            <?php endif; ?>
                            <?php if (User::hasPermission('editUserEmail')): ?>
                                <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
                                <?= $form->field($model, 'email_confirmed')->checkbox() ?>
                            <?php endif; ?>
                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => 124]) ?>
                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => 124]) ?>
                            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 124]) ?>
                            <?= $form->field($model, 'gender')->dropDownList(User::getGenderList()) ?>
                            <?= $form->field($model, 'birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname());?>

                            <?= $form->field($model, 'info')->textarea(['maxlength' => 255, 'rows' => 6]) ?>
                            <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(User::getStatusList()) ?>
                            <?= $form->field($model, 'registration_ip')->textInput(['readonly' => true]) ?>
                            <?= $form->field($model, 'skype')->textInput(['maxlength' => 64]) ?>
                            <?= $form->field($model, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                            <?= $form->field($model, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                            <?php if (User::hasPermission('bindUserToIp')): ?>
                                <?= $form->field($model, 'bind_to_ip')->textInput(['maxlength' => 255])->hint(Yii::t('art', 'For example') . ' : 123.34.56.78, 234.123.89.78') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::submitButtons($model, '/user/default/index', ['/user/default/delete', 'id' => $model->id]); ?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>











