<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \artsoft\auth\models\forms\FindingForm */

use yii\helpers\Html;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\ArtHelper;
use yii\widgets\MaskedInput;
use yii\captcha\Captcha;

$this->title = Yii::t('art/auth', 'Registration - user search');
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="signup-wrapper">
    <?php echo \yii\bootstrap\Alert::widget([
        'body' => '<i class="fa fa-info-circle"></i> Если Вы забыли свой логин и пароль, пройдите процедуру Регистрации или Восстнановления учетной записи.',
        'options' => ['class' => 'alert-info'],
    ]);
    ?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= $this->title ?></h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin([
                        'id' => 'form-signup-find',
                        'options' => ['autocomplete' => 'off'],
                        'validateOnBlur' => false,
                        'fieldConfig' => [

                        ],
                    ]); ?>
                    <div class="col-md-12">
                        <div class="row">
                            <?= $form->field($model, 'last_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
                            <?= $form->field($model, 'first_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
                            <?= $form->field($model, 'middle_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124])->hint('Пропустите это поле, если действительно нет отчества!') ?>
                            <?= $form->field($model, 'birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(\kartik\date\DatePicker::classname()); ?>

                            <?= $form->field($model, 'captcha')->widget(Captcha::className(), [
                                'template' => '<div class="row"><div class="col-sm-3">{image}</div><div class="col-sm-3">{input}</div></div>',
                                'captchaAction' => ['/auth/captcha']
                            ]) ?>

                            <?= Html::submitButton(Yii::t('art/auth', 'Signup'), ['class' => 'btn btn-primary btn-block', 'name' => 'find-button', 'value' => 'find']) ?>
                        </div>
                        <div class="row registration-block">
                            <div class="col-sm-6">
                                <?= Html::a(Yii::t('art/auth', "Enter"), ['default/login']) ?>
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

