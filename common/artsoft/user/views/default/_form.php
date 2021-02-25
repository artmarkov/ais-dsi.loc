<?php

use artsoft\helpers\Html;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\ArtHelper;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View $this
 * @var artsoft\models\User $model
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

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">

                    <?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

                    <?php if ($model->isNewRecord): ?>
                        <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
                        <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
                    <?php endif; ?>

                    <?php if (User::hasPermission('editUserEmail')): ?>
                        <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'gender')->dropDownList(User::getGenderList()) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'birth_day')->textInput(['maxlength' => 2]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'birth_month')->dropDownList(ArtHelper::getMonthsList()) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'birth_year')->textInput(['maxlength' => 4]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'info')->textarea(['maxlength' => 255, 'rows' => 6]) ?>

                </div>
                <div class="col-md-4">

                    <div class="form-group clearfix">
                        <label class="control-label" style="float: left; padding-right: 5px;">
                            <?= $model->attributeLabels()['registration_ip'] ?> :
                        </label>
                        <span><?= $model->registration_ip ?></span>
                    </div>


                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(User::getStatusList()) ?>

                    <?php if (User::hasPermission('editUserEmail')): ?>
                        <?= $form->field($model, 'email_confirmed')->checkbox() ?>
                    <?php endif; ?>

                    <?= $form->field($model, 'skype')->textInput(['maxlength' => 64]) ?>

                    <?= $form->field($model, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>

                    <?php if (User::hasPermission('bindUserToIp')): ?>
                        <?= $form->field($model, 'bind_to_ip')->textInput(['maxlength' => 255])->hint(Yii::t('art', 'For example') . ' : 123.34.56.78, 234.123.89.78') ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group ">
                    <?= Html::a(Yii::t('art', 'Go to list'), ['/user/default/index'], ['class' => 'btn btn-default']) ?>
                    <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                <?php if (!$model->isNewRecord): ?>
                    <?= Html::a(Yii::t('art', 'Delete'), ['/user/default/delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ])
                    ?>
                <?php endif; ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model'=>$model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>











