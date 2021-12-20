<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\Teachers;
use yii\widgets\Pjax;
use yii\widgets\MaskedInput;
use artsoft\helpers\RefBook;


/* @var $this yii\web\View */
/* @var $model common\models\activities\Activities */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $studyplan_id */

?>

    <div class="schedule-form">
<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin([
    'id' => 'schedule-form',
    'options' => [
        'data-pjax' => true
    ],
    'action' => !$model->isNewRecord ? ['subjectsect/schedule/update-schedule', 'id' => $model->id, 'studyplan_id' => $studyplan_id] : ['subjectsect/schedule/create-schedule', 'studyplan_id' => $studyplan_id],
    'enableClientValidation' => true,
]) ?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">

                    <?= $form->field($model, "direction_id")->dropDownList(['' => Yii::t('art/teachers', 'Select Direction...')] + \common\models\guidejob\Direction::getDirectionList(),
                        ['id' => $model->id . "-direction_id"]);?>
                    <?= $form->field($model, "teachers_id")->dropDownList(RefBook::find('teachers_fio')->getList())?>
                    <?= $form->field($model, "week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList())?>
                    <?= $form->field($model, "week_day")->dropDownList(['' => Yii::t('art/guide', 'Select week day...')] + \artsoft\helpers\ArtHelper::getWeekdayList()) ?>
                    <?= $form->field($model, "time_in")->textInput(['placeholder' => Yii::t('art/guide', 'Enter time in...')])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "time_out")->textInput(['placeholder' => Yii::t('art/guide', 'Enter time out...')])->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList()) ?>
                    <?= $form->field($model, "description")->textarea(['placeholder' => Yii::t('art/guide', 'Enter description...')])?>

                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::modalButtons('cancel-schedule', ['subjectsect/schedule/delete-schedule', 'id' => $model->id, 'studyplan_id' => $studyplan_id]); ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
        <?php Pjax::end(); ?>

    </div>
<?php
$js = <<<JS
$('.cancel-schedule').on('click', function (e) {
         e.preventDefault();
         closeModal();
          $.pjax.reload({container: '#studyplan-grid-pjax', async: true});
});

function closeModal() {
    $('#schedule-modal').modal('hide');
}
JS;

$this->registerJs($js);
?>