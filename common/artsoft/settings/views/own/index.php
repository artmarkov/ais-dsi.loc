<?php

use artsoft\helpers\Html;
use artsoft\settings\assets\SettingsAsset;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model artsoft\models\Setting */
/* @var $form artsoft\widgets\ActiveForm */

$this->title = 'Сведения об организации';
$this->params['breadcrumbs'][] = $this->title;

SettingsAsset::register($this);
?>
<div class="setting-index">
    <?php
    $form = ActiveForm::begin([
        'id' => 'setting-form',
        'validateOnBlur' => false,
        'fieldConfig' => [
            'template' => "<div class=\"settings-group\"><div class=\"settings-label\">{label}</div>\n<div class=\"settings-field\">{input}\n{hint}\n{error}</div></div>"
        ],
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint($model->getDescription('name')) ?>
                    <?= $form->field($model, 'shortname')->textInput(['maxlength' => true])->hint($model->getDescription('shortname')) ?>
                    <?= $form->field($model, 'address')->textInput(['maxlength' => true])->hint($model->getDescription('address')) ?>
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true])->hint($model->getDescription('email')) ?>
                    <?= $form->field($model, 'head')->textInput(['maxlength' => true])->hint($model->getDescription('head')) ?>
                    <?= $form->field($model, 'chief_accountant')->textInput(['maxlength' => true])->hint($model->getDescription('chief_accountant')) ?>
                    <?= $form->field($model, 'chief_accountant_post')->textInput(['maxlength' => true])->hint($model->getDescription('chief_accountant_post')) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <?= \artsoft\helpers\ButtonHelper::saveButton();?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>


