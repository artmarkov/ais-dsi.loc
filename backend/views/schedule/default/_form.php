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
                    <?= $form->field($model, "time_duration")->dropDownList(\common\models\schedule\SubjectSchedule::getDurationList()) ?>
                    <?= $form->field($model, "time_out")->textInput(['maxlength' => true, 'disabled' => true]) ?>
                    <?= $form->field($model, 'auditory_id')->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('auditory_memo_1')->getList(),
                        'showToggleAll' => false,
                        'options' => [
                            'placeholder' => Yii::t('art/guide', 'Select auditory...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            //'minimumInputLength' => 3,
                        ],

                    ]);
                    ?>

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

<?php
$script = <<< JS
$("#subjectschedule-time_duration").on('change', function(event, key) {
    let time_in = document.getElementById('subjectschedule-time_in').value;
    let time_duration = document.getElementById('subjectschedule-time_duration').value;
    //  console.log(time_duration);
    $.ajax({
            url: '/admin/schedule/default/select',
            type: 'POST',
            data: {
                time_in: time_in,
                time_duration: time_duration
            },
            success: function (val) {
                   let  p = jQuery.parseJSON(val);
                   // console.log(val);
                   document.getElementById('subjectschedule-time_out').value = p.time_out;
            },
            error: function () {
                alert('Error!!!');
            }
        });

});
JS;
$this->registerJs($script, \yii\web\View::POS_END);
?>
