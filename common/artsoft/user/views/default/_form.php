<?php

use artsoft\helpers\Html;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\ArtHelper;
use yii\widgets\MaskedInput;
use kartik\date\DatePicker;

/**
 * @var yii\web\View $this
 * @var artsoft\models $model
 * @var artsoft\widgets\ActiveForm $form
 */
//print_r($model->getVersions());
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
            Информация о пользователе
            <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/user/default/history', 'id' => $model->id]); ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Регистрационные данные
                </div>
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

                            <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(User::getStatusList()) ?>
                            <?= $form->field($model, 'registration_ip')->textInput(['readonly' => true]) ?>
                            <?php if (User::hasPermission('bindUserToIp')): ?>
                                <?= $form->field($model, 'bind_to_ip')->textInput(['maxlength' => 255])->hint(Yii::t('art', 'For example') . ' : 123.34.56.78, 234.123.89.78') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if (!$model->isNewRecord): ?>
                    <div class="panel-footer">
                        <div class="form-group btn-group">
                            <?= Html::a('<i class="fa fa-envelope-o" aria-hidden="true"></i> ' . Yii::t('art', 'Send registration data'),
                                ['/user/default/send-login', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-default',
                                    'data' => [
                                        'confirm' => Yii::t('yii', 'Are you sure you want to send registration data?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            ?>

                            <?= Html::a('<i class="fa fa-user-secret" aria-hidden="true"></i> ' . Yii::t('art', 'Login as user'),
                                ['/user/default/secret-login', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-warning',

                                ]);
                            ?>

                            <?= Html::a('<i class="fa fa-shield" aria-hidden="true"></i> ' . Yii::t('art/user', 'Permissions'),
                                ['user-permission/set', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-primary',

                                ]);
                            ?>

                            <?= Html::a('<i class="fa fa-key" aria-hidden="true"></i> ' . Yii::t('art/user', 'Password'),
                                ['default/change-password', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-danger',

                                ]);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Связанные данные
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'user_category')->dropDownList(User::getUserCategoryList(), ['disabled' => Yii::$app->user->isSuperadmin ? false : true]) ?>
                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => 124]) ?>
                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => 124]) ?>
                            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 124]) ?>
                            <?= $form->field($model, 'gender')->dropDownList(User::getGenderList()) ?>
                            <?= $form->field($model, 'birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname()); ?>
                            <?= $form->field($model, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                            <?= $form->field($model, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                            <?= $form->field($model, 'skype')->textInput(['maxlength' => 64]) ?>
                            <?= $form->field($model, 'info')->textarea(['maxlength' => 255, 'rows' => 6]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model, '/user/default/index', ['/user/default/delete', 'id' => $model->id]); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>











