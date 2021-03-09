<?php

use artsoft\helpers\Html;
use artsoft\settings\assets\SettingsAsset;
use artsoft\settings\models\GeneralSettings;
use artsoft\widgets\ActiveForm;
use artsoft\widgets\LanguagePills;

/* @var $this yii\web\View */
/* @var $model artsoft\models\Setting */
/* @var $form artsoft\widgets\ActiveForm */

$this->title = Yii::t('art/settings', 'General Settings');
$this->params['breadcrumbs'][] = $this->title;

SettingsAsset::register($this);
?>
<div class="setting-index">
    <?php
    $form = ActiveForm::begin([
        'id' => 'setting-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true])/*->hint($model->getDescription('description')) */ ?>

                    <?= $form->field($model, 'email')->textInput(['maxlength' => true])->hint($model->getDescription('email')) ?>

                    <?= $form->field($model, 'phone')->textInput(['maxlength' => true])->hint($model->getDescription('phone')) ?>

                    <?= $form->field($model, 'facebook')->textInput(['maxlength' => true])->hint($model->getDescription('facebook')) ?>

                    <?= $form->field($model, 'instagram')->textInput(['maxlength' => true])->hint($model->getDescription('instagram')) ?>

                    <?= $form->field($model, 'timezone')->dropDownList(GeneralSettings::getTimezones())->hint($model->getDescription('timezone')) ?>

                    <?= $form->field($model, 'dateformat')->dropDownList(GeneralSettings::getDateFormats())->hint($model->getDescription('dateformat')) ?>

                    <?= $form->field($model, 'timeformat')->dropDownList(GeneralSettings::getTimeFormats())->hint($model->getDescription('timeformat')) ?>
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

