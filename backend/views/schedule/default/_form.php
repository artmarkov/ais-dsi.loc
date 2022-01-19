<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use common\models\guidejob\Direction;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSchedule */
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
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

<!--                    --><?//= Html::activeHiddenInput($model, 'subject_sect_studyplan_id') ?>

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
