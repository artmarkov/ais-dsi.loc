<?php

//use yii\bootstrap\ActiveForm;
use artsoft\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var artsoft\auth\models\forms\UpdatePasswordForm $model
 */
$this->title = Yii::t('art/auth', 'Update Password');
$this->params['breadcrumbs'][] = $this->title;

?>
    <div id="update-wrapper">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= $this->title ?></h3>
                    </div>
                    <div class="panel-body">

                        <?php $form = ActiveForm::begin([
                            'id' => 'update-form',
                            'options' => ['autocomplete' => 'off'],
                            'validateOnBlur' => false,
                        ]) ?>

                        <?php if ($model->scenario != 'restoreViaEmail'): ?>
                            <?= $form->field($model, 'current_password')->passwordInput(['maxlength' => 255]) ?>
                        <?php endif; ?>

                        <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255]) ?>

                        <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255]) ?>

                        <?= Html::submitButton(Yii::t('art', 'Update'), ['class' => 'btn btn-primary btn-block']) ?>

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
CSS;

$this->registerCss($css);
?>