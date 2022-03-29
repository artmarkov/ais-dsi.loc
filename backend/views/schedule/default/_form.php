<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\schedule\SubjectSchedule */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="subject-schedule-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'subject-schedule-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Элемент расписания занятий:
            <?php echo RefBook::find('subject_memo_2')->getValue($teachersLoadModel->studyplan_subject_id); ?>
            <?php echo RefBook::find('sect_name_1')->getValue($teachersLoadModel->subject_sect_studyplan_id); ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?php if ($model->isSubjectMontly()): ?>
                        <?= $form->field($model, "week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList()) ?>
                    <?php endif; ?>
                    <?= $form->field($model, "week_day")->dropDownList(['' => Yii::t('art/guide', 'Select week day...')] + \artsoft\helpers\ArtHelper::getWeekdayList()) ?>
                    <?= $form->field($model, "time_in")->textInput()->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "time_out")->textInput()->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList()) ?>
                    <?= $form->field($model, "description")->textarea() ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
