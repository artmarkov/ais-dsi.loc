<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\guidejob\Direction;
use common\models\teachers\TeachersPlan;
use artsoft\helpers\Html;
use common\models\user\UserCommon;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\schedule\ConsultSchedule */
/* @var $teachersLoadModel common\models\teachers\TeachersLoad */
/* @var $form artsoft\widgets\ActiveForm */
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
            <?php echo RefBook::find('subject_memo_2')->getValue($teachersLoadModel->studyplan_subject_id); ?>
            <?php echo RefBook::find('sect_name_1')->getValue($teachersLoadModel->subject_sect_studyplan_id); ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'datetime_in')->widget(kartik\datetime\DateTimePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(['autocomplete' => 'off', /*'disabled' => $readonly*/]); ?>

                    <?= $form->field($model, 'datetime_out')->widget(kartik\datetime\DateTimePicker::classname())->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(['autocomplete' => 'off', /*'disabled' => $readonly*/]) ?>

                    <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList()) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
