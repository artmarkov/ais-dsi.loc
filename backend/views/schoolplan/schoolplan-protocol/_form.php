<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanProtocol */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="schoolplan-protocol-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'schoolplan-protocol-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка протокола мероприятия
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    if ($model->isNewRecord) {
                        echo Html::activeHiddenInput($model, "schoolplan_id");
                    }
                    ?>

                    <?= $form->field($model, 'protocol_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'protocol_date')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true]) ?>

                    <?= $form->field($model, 'leader_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'value' => $model->leader_id,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);

                    ?>
                    <?= $form->field($model, 'secretary_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'value' => $model->secretary_id,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);

                    ?>
                    <?= $form->field($model, 'members_list')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\models\User::getUsersListByCategory(['teachers']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);

                    ?>
                    <?= $form->field($model, 'subject_list')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\RefBook::find('subject_name')->getList(),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);

                    ?>

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
