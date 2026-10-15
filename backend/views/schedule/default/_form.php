<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\schedule\SubjectSchedule */
/* @var $form artsoft\widgets\ActiveForm */
$subject = '';
if ($teachersLoadModel->studyplan_subject_id != 0) {
    $subject = Yii::$app->db->createCommand(' select concat(subject, \' [\', sect_name, \']\')
                    FROM subject_schedule_view 
                    where  studyplan_subject_id = :studyplan_subject_id ',
        ['studyplan_subject_id' => $teachersLoadModel->studyplan_subject_id,
        ])->queryScalar();
} else {
    $subject = Yii::$app->db->createCommand(' select concat(subject, \' [\', sect_name, \']\')
                    FROM subject_schedule_view 
                    where subject_sect_studyplan_id = :subject_sect_studyplan_id',
        ['subject_sect_studyplan_id' => $teachersLoadModel->subject_sect_studyplan_id,
        ])->queryScalar();
}
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
            <?php //echo RefBook::find('subject_memo_2')->getValue($teachersLoadModel->studyplan_subject_id); ?>
            <?php //echo RefBook::find('sect_name_1')->getValue($teachersLoadModel->subject_sect_studyplan_id); ?>
            <?php echo $subject; ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php if (\artsoft\Art::isBackend()): ?>
                    <?= $form->field($model, 'teachers_load_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\schedule\SubjectScheduleView::getDirectionVidListByTeachersLoadId($teachersLoadModel->teachers_id,$teachersLoadModel->direction_id,$teachersLoadModel->studyplan_subject_id,$teachersLoadModel->subject_sect_studyplan_id),
                            'hideSearch' => true,
                            'options' => [
                            'id' => 'teachers_id',
                            'disabled' => false,
//                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])->label('Вид деятельности');
                    ?>
                    <?php endif; ?>
                    <?php if ($model->isSubjectMontly()): ?>
                        <?= $form->field($model, "week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList()) ?>
                    <?php endif; ?>
                    <?= $form->field($model, "week_day")->dropDownList(['' => Yii::t('art/guide', 'Select week day...')] + \artsoft\helpers\ArtHelper::getWeekdayList()) ?>
                    <?= $form->field($model, "time_in")->textInput()->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "time_duration")->dropDownList(['' => Yii::t('art/guide', 'Select...')] +\common\models\schedule\SubjectSchedule::getDurationList()) ?>
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
