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

?>

<div id="signup-wrapper">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= $this->title ?></h3>
                </div>
                <div class="panel-body">

                    <?php $form = ActiveForm::begin([
                        'id' => 'signup',
                        'validateOnBlur' => false,
                        'options' => ['autocomplete' => 'off'],
                    ]); ?>
                    <div class="col-md-12">
                        <div class="row">
                            <h4>  Ваш логин: <b><?= $model->username; ?></b></h4>

                            <?= $form->field($model, 'username')->label(false)->hiddenInput(['value' => $model->username]) ?>

                            <?= $form->field($model, 'id')->label(false)->hiddenInput(['value' => $model->id]) ?>

                            <?= $form->field($model, 'email')->textInput(['placeholder' => $model->getAttributeLabel('email'), 'autocomplete' => 'off', 'maxlength' => 255])->hint('Для регистрации необходимо ввести E-mail') ?>

                            <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password'), 'maxlength' => 255])->hint('Пароль не менее 6-ти символов') ?>

                            <?= $form->field($model, 'repeat_password')->passwordInput(['placeholder' => $model->getAttributeLabel('repeat_password'), 'maxlength' => 255])->hint('Подтвердите пароль') ?>

                            <?= Html::submitButton(Yii::t('art/auth', 'Signup'), ['class' => 'btn btn-primary btn-block']) ?>
                        </div>
                        <div class="row registration-block">
                            <div class="col-sm-6">
                                <?= Html::a(Yii::t('art/auth', "Login"), ['default/login']) ?>
                            </div>
                            <div class="col-sm-6 text-right">
                                <?= Html::a(Yii::t('art/auth', "Forgot password?"), ['default/reset-password']) ?>
                            </div>
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


















