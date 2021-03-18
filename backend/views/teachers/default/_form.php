<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use nex\chosen\Chosen;
use yii\widgets\MaskedInput;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Teachers */
/* @var $form artsoft\widgets\ActiveForm */

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Деятельность: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Деятельность: " + (index + 1))
    });
});
';

$this->registerJs($js);
?>

<div class="teachers-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'teachers-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
    ])

    ?>
    <div class="panel">
        <div class="panel-heading">
            Информация о преподавателе
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(Teachers::getStatusList()) ?>
                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-heading">
                    Основные сведения
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($modelUser, 'last_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($modelUser, 'first_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($modelUser, 'middle_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($modelUser, 'gender')->dropDownList(artsoft\models\User::getGenderList()) ?>

                            <?= $form->field($modelUser, 'birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname()); ?>

                            <?= $form->field($modelUser, 'snils')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput() ?>

                            <?= $form->field($modelUser, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>

                            <?= $form->field($modelUser, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-heading">
                    Должностные характеристики
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            echo $form->field($model, 'position_id')->dropDownList(common\models\guidejob\Position::getPositionList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Position...'),
                                'id' => 'position_id'
                            ])->label(Yii::t('art/teachers', 'Name Position'));
                            ?>

                            <?php
                            echo $form->field($model, 'level_id')->dropDownList(common\models\guidejob\Level::getLevelList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Level...'),
                                'id' => 'level_id'
                            ])->label(Yii::t('art/teachers', 'Name Level'));
                            ?>

                            <?= $form->field($model, 'tab_num')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'year_serv')->textInput() ?>

                            <?= $form->field($model, 'time_serv_init')->widget(DatePicker::classname())->label(Yii::t('art/teachers', 'For date')); ?>

                            <?= $form->field($model, 'year_serv_spec')->textInput() ?>

                            <?= $form->field($model, 'time_serv_spec_init')->widget(DatePicker::classname())->label(Yii::t('art/teachers', 'For date')); ?>

                            <?php
                            echo $form->field($model, 'department_list')->widget(Chosen::className(), [
                                'items' => Teachers::getDepartmentList(),
                                'options' => ['disabled' => $readonly],
                                'multiple' => true,
                                'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                            ])->label(Yii::t('art/guide', 'Department'));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 4, // the maximum times, an element can be added (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsActivity[0],
                'formId' => 'teachers-form',
                'formFields' => [
                    'work_id',
                    'direction_id',
                    'stake_id',
                ],
            ]); ?>

            <div class="panel panel-info">
                <div class="panel-heading">
                    Сведения о трудовой деятельности

                </div>
                <div class="panel-body">
                    <div class="container-items"><!-- widgetBody -->
                        <?php foreach ($modelsActivity as $index => $modelActivity): ?>
                            <div class="item panel panel-info"><!-- widgetItem -->
                                <div class="panel-heading">
                                    <span class="panel-title-activities">Деятельность: <?= ($index + 1) ?></span>
                                    <?php if (!$readonly): ?>
                                        <div class="pull-right">
                                            <button type="button" class="remove-item btn btn-default btn-xs">удалить
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    // necessary for update action.
                                    if (!$modelActivity->isNewRecord) {
                                        echo Html::activeHiddenInput($modelActivity, "[{$index}]id");
                                    }
                                    ?>
                                    <?= $form->field($modelActivity, "[{$index}]work_id")->dropDownList(common\models\guidejob\Work::getWorkList(), [
                                        'prompt' => Yii::t('art/teachers', 'Select Work...'),
                                        'id' => 'work_id'
                                    ])->label(Yii::t('art/teachers', 'Name Work'));
                                    ?>
                                    <?= $form->field($modelActivity, "[{$index}]direction_id")->dropDownList(\common\models\guidejob\Direction::getDirectionList(), [
                                        'prompt' => Yii::t('art/teachers', 'Select Direction...'),
                                        'id' => 'direction_id'
                                    ])->label(Yii::t('art/teachers', 'Name Direction'));
                                    ?>
                                    <?= $form->field($modelActivity, "[{$index}]stake_id")->dropDownList(\common\models\guidejob\Stake::getStakeList(), [
                                        'prompt' => Yii::t('art/teachers', 'Select Stake...'),
                                        'id' => 'direction_id'
                                    ])->label(Yii::t('art/teachers', 'Name Stake'));
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div><!-- .panel -->
                <?php if (!$readonly): ?>
                    <div class="panel-footer">
                        <div class="form-group btn-group">
                            <button type="button" class="add-item btn btn-success btn-sm pull-right">
                                <i class="glyphicon glyphicon-plus"></i> Добавить
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php DynamicFormWidget::end(); ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Сведения о достижениях
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">

                                <?php
                                echo $form->field($model, 'bonus_list')->widget(Chosen::className(), [
                                    'items' => Teachers::getBonusItemList(),
                                    'options' => ['disabled' => $readonly],
                                    'multiple' => true,
                                    'placeholder' => Yii::t('art/teachers', 'Select Teachers Bonus...'),
                                ])->label(Yii::t('art/teachers', 'Teachers Bonus'));
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
