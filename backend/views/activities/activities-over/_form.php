<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\activities\ActivitiesOver;
use artsoft\helpers\Html;
use common\models\own\Department;

/* @var $this yii\web\View */
/* @var $model common\models\activities\ActivitiesOver */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="activities-over-form">
    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'activities-over-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка мероприятия
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'over_category')->widget(\kartik\select2\Select2::class, [
                        'data' => $model->getOverCategoryList(),
                        'options' => [
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'disabled' => true,
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'datetime_in')->widget(kartik\datetime\DateTimePicker::class)->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'datetime_out')->widget(kartik\datetime\DateTimePicker::class)->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly]) ?>

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'department_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => Department::getDepartmentList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Department'));
                    ?>

                    <?= $form->field($model->loadDefaultValues(), 'executors_list')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\user\UserCommon::getUsersCommonListByCategory(['teachers', 'employees', 'students']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'minimumInputLength' => 3,
                            'maximumSelectionLength' => (!$model->isNewRecord && $model->over_category == 2) ? 1 : false,
                        ],

                    ]);
                    ?>
                    <?= $form->field($model, "auditory_id")->dropDownList(RefBook::find('auditory_memo_1')->getList(), [
                        'prompt' => Yii::t('art/guide', 'Select auditory...'), 'disabled' => $readonly
                    ])
                    ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

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
