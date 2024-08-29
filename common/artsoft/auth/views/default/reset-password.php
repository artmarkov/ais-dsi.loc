<?php

//use yii\bootstrap\ActiveForm;
use artsoft\widgets\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var artsoft\auth\models\forms\PasswordRecoveryForm $model
 */
$this->title = Yii::t('art/auth', 'Reset Password');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert-alert-warning text-center">
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php endif; ?>

    <div id="update-wrapper">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= $this->title ?></h3>
                    </div>
                    <div class="panel-body">

                        <?php
                        $form = ActiveForm::begin([
                            'id' => 'reset-form',
                            'options' => ['autocomplete' => 'off'],
                            'validateOnBlur' => false,
                        ]);
                        ?>

                        <?= $form->field($model, 'username')->textInput(['maxlength' => 50]) ?>

                        <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

                        <?= $form->field($model, 'captcha')->widget(Captcha::className(), [
                            'template' => '<div class="row"><div class="col-sm-3">{image}</div><div class="col-sm-3">{input}</div></div>',
                            'captchaAction' => ['/auth/captcha']
                        ]) ?>

                        <?= Html::submitButton(Yii::t('art/auth', 'Reset'), ['class' => 'btn btn-primary btn-block']) ?>
                        <div class="row registration-block">
                            <div class="col-sm-12 text-right">
                                <?= Html::a(Yii::t('art/auth', "Forgot login?"), ['default/finding']) ?>
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
#update-wrapper {
	position: relative;
	margin-top: 30px;
}
#update-wrapper .registration-block {
	margin-top: 15px;
}
CSS;

$this->registerCss($css);
?>