<?php

use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use common\models\schoolplan\SchoolplanActivity;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanActivity */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="schoolplan-activity-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'schoolplan-activity-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка работы мероприятия
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?php
                    if ($model->isNewRecord) {
                        echo Html::activeHiddenInput($model, "schoolplan_id");
                    }
                    if (\artsoft\Art::isFrontend()) {
                        echo Html::activeHiddenInput($model, 'author_id');
                    } else {
                        echo $form->field($model->loadDefaultValues(), 'author_id')->widget(\kartik\select2\Select2::class, [
                            'data' => artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                            'showToggleAll' => false,
                            'options' => [
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                                //'minimumInputLength' => 3,
                            ],

                        ]);
                    }
                    ?>

                    <?= $form->field($model, 'executor_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\Art::isFrontend() ? $model->getUsersListForExecutors() : artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'datetime_in')->widget(kartik\datetime\DateTimePicker::class)->widget(\yii\widgets\MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_time_mask')])->textInput(['autocomplete' => 'off', 'disabled' => $readonly])->hint('Выберите запланированную дату и укажите время проведения работы.');?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>

                    <?= $form->field($model, 'places')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>

                    <?= $form->field($model, 'author_comment')->textarea(['rows' => 6, 'disabled' => $readonly]) ?>

                    <?= $form->field($model, 'activity_status')->dropDownList(\common\models\schoolplan\SchoolplanActivity::getStatusExeList(), ['disabled' => false]) ?>

                    <?= $form->field($model, 'activity_status_reason')->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : (\artsoft\Art::isBackend() ? \artsoft\helpers\ButtonHelper::viewButtons($model) : \artsoft\helpers\ButtonHelper::exitButton()); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
$("select[name='SchoolplanActivity[activity_status]']").find(":selected").val() === '3' ? $('.field-schoolplanactivity-activity_status_reason').show() : $('.field-schoolplanactivity-activity_status_reason').hide();
document.getElementById("schoolplanactivity-activity_status").onchange = function () {
 $(this).val() === '3' ? $('.field-schoolplanactivity-activity_status_reason').show() : $('.field-schoolplanactivity-activity_status_reason').hide();
}
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>