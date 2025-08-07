<?php

use artsoft\helpers\Html;
use artsoft\settings\assets\SettingsAsset;
use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;

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
                    Модуль: Логи и журналы
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?= $form->field($model, 'shelf_life_pass')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'shelf_life_attendlog')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'shelf_life_sitelog')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'shelf_life_requestlog')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'shelf_life_dbdump')->textInput(['maxlength' => true]) ?>

                        </div>
                    </div>
                </div>
            </div>
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
            <div class="panel panel-default">
                <div class="panel-heading">
                    Модуль: Учебная работа
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?= $form->field($model, 'study_plan_month_in')->textInput(['maxlength' => true])->hint('Введите месяц') ?>
                            <?= $form->field($model, 'study_plan_month_in_frontend')->textInput(['maxlength' => true])->hint('Введите месяц') ?>
                            <?= $form->field($model, 'thematic_template_author_flag')->radioList([0 => 'Доступны только авторам', 1 => 'Доступны всем']) ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Модуль: Дистанционная запись на обучение
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?= $form->field($model, 'pre_status')->radioList([0 => 'Закрыт', 1 => 'Открыт']) ?>
                            <?= $form->field($model, 'pre_date_in')->widget(DatePicker::class)->hint('Введите дату открытия записи') ?>
                            <?= $form->field($model, 'pre_date_out')->widget(DatePicker::class)->hint('Введите дату закрытия записи') ?>
                            <?= $form->field($model, 'pre_plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList())->hint('Введите учебный год на каторый ведется приеи') ?>
                            <?= $form->field($model, 'pre_date_start')->widget(DatePicker::class)->hint('Введите дату начала обучения') ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Модуль: Оплата за обучение
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?= $form->field($model, 'debtors_days')->textInput(['maxlength' => true])->hint('Введите колличество дней') ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Модуль: График работы преподавателей
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?= $form->field($model, 'generator_time_limit')->textInput(['maxlength' => true])->hint('Введите колличество часов') ?>
                            <?= $form->field($model, 'generator_time_per')->textInput(['maxlength' => true])->hint('Введите колличество минут') ?>
                            <?= $form->field($model, 'generator_time_max')->textInput(['maxlength' => true])->hint('Введите час окончания работы. Например: 20 или 21.') ?>

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


