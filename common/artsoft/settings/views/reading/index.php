<?php

use artsoft\helpers\Html;
use artsoft\settings\assets\SettingsAsset;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model artsoft\models\Setting */
/* @var $form artsoft\widgets\ActiveForm */

$this->title = Yii::t('art/settings', 'Reading Settings');
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

                    <?= $form->field($model, 'page_size')->textInput(['maxlength' => true])->hint($model->getDescription('page_size')) ?>
                    <?= $form->field($model, 'phone_mask')->textInput(['maxlength' => true])->hint($model->getDescription('phone_mask')) ?>
                    <?= $form->field($model, 'date_mask')->textInput(['maxlength' => true])->hint($model->getDescription('date_mask')) ?>
                    <?= $form->field($model, 'time_mask')->textInput(['maxlength' => true])->hint($model->getDescription('time_mask')) ?>
                    <?= $form->field($model, 'date_time_mask')->textInput(['maxlength' => true])->hint($model->getDescription('date_time_mask')) ?>
                    <?= $form->field($model, 'snils_mask')->textInput(['maxlength' => true])->hint($model->getDescription('snils_mask')) ?>
                    <?= $form->field($model, 'coordinate_mask')->textInput(['maxlength' => true])->hint($model->getDescription('coordinate_mask')) ?>

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


