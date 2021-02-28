<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
//use artsoft\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\captcha\Captcha;

$this->title = Yii::t('art/auth', 'Registration - user search');
$this->params['breadcrumbs'][] = $this->title;

$col12 = $this->context->module->gridColumns;
$col9 = (int) ($col12 * 3 / 4);
$col6 = (int) ($col12 / 2);
$col4 = (int) ($col12 / 3);
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
                    <?php $form = ActiveForm::begin(['id' => 'form-signup-find']); ?>

                    <div class="row">
                        <div class="col-md-<?= $col4 ?>">
                            <!--                                                                              'placeholder' => $model->getAttributeLabel('middle_name'),-->
                            <?= $form->field($model, 'last_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-<?= $col4 ?>">
                            <?= $form->field($model, 'first_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-<?= $col4 ?>">
                            <?= $form->field($model, 'middle_name')->textInput([ 'autocomplete' => 'off', 'maxlength' => 124]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-<?= $col6 ?>">
                            <?= $form->field($model, 'birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput() ?>
                        </div>
                    </div>

<!--                    --><?//= $form->field($model, 'captcha')->widget(Captcha::className(), [
//                        'template' => '<div class="row"><div class="col-sm-' . $col3 . '">{image}</div><div class="col-sm-' . $col3 . '">{input}</div></div>',
//                        'captchaAction' => ['/auth/captcha']
//                    ]) ?>
                    <hr>

                    <?= Html::submitButton(Yii::t('art/auth', 'Signup'), ['class' => 'btn btn-primary btn-block', 'name' => 'find-button', 'value' => 'find']) ?>
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

