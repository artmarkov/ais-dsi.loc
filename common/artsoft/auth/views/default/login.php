<?php

/**
 * @var $this yii\web\View
 * @var $model artsoft\auth\models\forms\LoginForm
 */
use artsoft\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('art/auth', 'Authorization');
$this->params['breadcrumbs'][] = $this->title;

?>

    <div id="login-wrapper">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
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
                            <div class="col-sm-6">
                                <?= Html::a(Yii::t('art/auth', "Registration"), ['default/finding']) ?>
                            </div>
                            <div class="col-sm-6 text-right">
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