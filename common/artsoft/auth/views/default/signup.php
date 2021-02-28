<?php

//use yii\bootstrap\ActiveForm;
use artsoft\widgets\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var artsoft\auth\models\forms\RegistrationForm $model
 */
$this->title = Yii::t('art/auth', 'Signup');
$this->params['breadcrumbs'][] = $this->title;

$col12 = $this->context->module->gridColumns;
$col9 = (int) ($col12 * 3 / 4);
$col6 = (int) ($col12 / 2);
$col3 = (int) ($col12 / 4);
?>

<div id="signup-wrapper">
    <div class="row">
        <div class="col-md-<?= $col6 ?> col-md-offset-<?= $col3 ?>">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= $this->title ?></h3>
                </div>
                <div class="panel-body">

                    <?php $form = ActiveForm::begin([
                        'id' => 'signup',
                        'validateOnBlur' => false,
                        'options' => ['autocomplete' => 'off'],
                    ]); ?>

                    <h4>  <?= Yii::t('art/auth', 'Login')?>: <b><?= $model->username; ?></b></h4>

                    <?= $form->field($model, 'username')->label(false)->hiddenInput(['value' => $model->username]) ?>

                    <?= $form->field($model, 'id')->label(false)->hiddenInput(['value' => $model->id]) ?>

                    <?= $form->field($model, 'email')->textInput(['placeholder' => $model->getAttributeLabel('email'), 'autocomplete' => 'off', 'maxlength' => 255]) ?>

                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password'),'maxlength' => 255]) ?>

                    <?= $form->field($model, 'repeat_password')->passwordInput(['placeholder' => $model->getAttributeLabel('repeat_password'),'maxlength' => 255]) ?>

                    <hr>


                    <?= Html::submitButton(Yii::t('art/auth', 'Signup'), ['class' => 'btn btn-primary btn-block']) ?>

                    <div class="row registration-block">
                        <div class="col-sm-<?= $col6 ?>">
                            <?= Html::a(Yii::t('art/auth', "Login"), ['default/login']) ?>
                        </div>
                        <div class="col-sm-<?= $col6 ?> text-right">
                            <?= Html::a(Yii::t('art/auth', "Forgot password?"), ['default/reset-password']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$css = <<<CSS

#signup-wrapper {
	position: relative;
	margin-top: 30px;
}
#signup-wrapper .registration-block {
	margin-top: 15px;
}
CSS;

$this->registerCss($css);
?>


















