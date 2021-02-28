<?php

/**
 * @var $this yii\web\View
 * @var $model artsoft\auth\models\forms\LoginForm
 */
use artsoft\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('art/auth', 'Authorization');
$this->params['breadcrumbs'][] = $this->title;

$col12 = $this->context->module->gridColumns;
$col9 = (int)($col12 * 3 / 4);
$col6 = (int)($col12 / 2);
$col3 = (int)($col12 / 4);
?>

    <div id="login-wrapper">
        <div class="row">
            <div class="col-md-<?= $col6 ?> col-md-offset-<?= $col3 ?>">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= $this->title ?></h3>
                    </div>
                    <div class="panel-body">

                        <?php
                        $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'options' => ['autocomplete' => 'off'],
                            'validateOnBlur' => false,
                            'fieldConfig' => [
                                'template' => "{input}\n{error}",
                            ],
                        ])
                        ?>

                        <?= $form->field($model, 'username')->textInput(['placeholder' => $model->getAttributeLabel('username'), 'autocomplete' => 'off']) ?>

                        <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password'), 'autocomplete' => 'off']) ?>

                        <?= $form->field($model, 'rememberMe')->checkbox(['value' => true]) ?>

                        <?= Html::submitButton(Yii::t('art/auth', 'Login'), ['class' => 'btn btn-primary btn-block']) ?>

                        <div class="row registration-block">
                            <div class="col-sm-<?= $col6 ?>">
                                <?= Html::a(Yii::t('art/auth', "Registration"), ['default/finding']) ?>
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

#login-wrapper {
	position: relative;
	margin-top: 30px;
}
#login-wrapper .registration-block {
	margin-top: 15px;
}
CSS;

$this->registerCss($css);
?>