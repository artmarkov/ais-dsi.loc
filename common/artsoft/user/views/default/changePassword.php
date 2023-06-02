<?php

use artsoft\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var artsoft\models\User $model
 */
$this->title = Yii::t('art/user', 'Update Password for "{user}"', ['user' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/user', $model->username), 'url' => ['/user/default/update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-update">
    <?php $form = ActiveForm::begin([
        'id' => 'user',
        'layout' => 'horizontal',
    ]); ?>
    <div class="user-form">
        <div class="panel">
            <div class="panel-heading">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">

                                <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

                                <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= \artsoft\helpers\ButtonHelper::saveButton();?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
