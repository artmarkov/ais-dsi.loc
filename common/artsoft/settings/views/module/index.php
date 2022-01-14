<?php

use artsoft\helpers\Html;
use artsoft\settings\assets\SettingsAsset;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model artsoft\models\Setting */
/* @var $form artsoft\widgets\ActiveForm */

$this->title = 'Настройки модулей';
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
            <div class="panel panel-default">
                <div class="panel-heading">
                    Модуль: Показатели эффективности
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?= $form->field($model, 'day_in')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'day_out')->textInput(['maxlength' => true]) ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Модуль: Расписание занятий
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?= $form->field($model, 'student_delta_time')->textInput(['maxlength' => true])->hint('Введите параметр в секундах') ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <?= \artsoft\helpers\ButtonHelper::saveButton(); ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>


