<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\schedule\ConsultSchedule */
/* @var $teachersLoadModel common\models\teachers\TeachersLoad */
/* @var $form artsoft\widgets\ActiveForm */

$readonly = false;
?>

<div class="teachers-plan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'teachers-plan-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Элемент расписания консультаций:
            <?php echo RefBook::find('subject_memo_4')->getValue($teachersLoadModel->studyplan_subject_id); ?>
            <?php echo RefBook::find('sect_name_1')->getValue($teachersLoadModel->subject_sect_studyplan_id); ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="col-sm-12">
                <?php echo \yii\bootstrap\Alert::widget([
                    'body' => '<i class="fa fa-info-circle"></i> Совет: Если Вам необходимо заполнить несколько занятий для одного ученика, нажимайте кнопку "Сохранить и добавить".',
                    'options' => ['class' => 'alert-info'],
                ]);
                ?>
            </div>
            <div class="row">
                <div class="col-sm-12">

                    <div class="col-sm-12">
                        <?= $form->field($model, 'date_in')->widget(\kartik\date\DatePicker::className(), ['pluginOptions' => [
                            'orientation' => 'bottom', 'autoclose' => true,
                        ]])->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                        <?= $form->field($model, 'time_in')->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.time_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                        <?= $form->field($model, 'time_out')->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.time_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>
                    </div>
                    <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList()) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
                <?= \artsoft\helpers\ButtonHelper::saveButton('submitAction', 'savenext', 'Save & Add', 'btn-md'); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
