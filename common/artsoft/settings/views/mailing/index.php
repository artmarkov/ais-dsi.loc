<?php

use artsoft\helpers\Html;
use artsoft\settings\assets\SettingsAsset;
use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model artsoft\models\Setting */
/* @var $form artsoft\widgets\ActiveForm */

$this->title = 'Рассылки и оповещения';
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
                    Уведомления о датах рождения сотрудников и преподавателей
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?= $form->field($model, 'mailing_birthday')->textInput(['maxlength' => true])->hint('Введите E-mail через запятую.') ?>

                            <?= $form->field($model, 'mailing_birthday_period')->textInput(['maxlength' => true])->hint('Введите E-mail через запятую.') ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Согласования и уведомления
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?= $form->field($model, 'schoolplan_perform_doc')->checkbox()->hint('Поставьте флажок чтобы включить уведомления в модуле "Выполнение плана и участие в мероприятии".') ?>

                            <?= $form->field($model, 'confirm_progress_perform_doc')->checkbox()->hint('Поставьте флажок чтобы включить уведомления в модуле "Проверка журнала успеваемости".') ?>

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


