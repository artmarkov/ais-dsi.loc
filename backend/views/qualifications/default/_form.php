<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model common\models\teachers\TeachersQualifications */

?>

<div class="teachers-qualifications-form">
    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'teachers-qualifications-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            Карточка ППК <?php echo RefBook::find('teachers_fullname')->getValue($modelTeachers->id); ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <?= Html::activeHiddenInput($model, "teachers_id") ?>

                <?= $form->field($model, 'status')->radioList(\common\models\teachers\TeachersQualifications::getStatusList()) ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'readonly' => $readonly])->hint('Введите название ППК или переподготовки') ?>

                <?= $form->field($model, 'place')->textInput(['maxlength' => true, 'readonly' => $readonly])->hint('Напишите название учреждения и город, где Вы проходили обучение(повышение квалификации или перподготовку)') ?>

                <?= $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true, 'disabled' => $readonly])->hint('Кратко опишите ППК(необязательно)') ?>

                <?= $form->field($model, 'date')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>
            </div>
            <?php if (!$model->isNewRecord) : ?>
                <div class="panel panel-info qualifications_info">
                    <div class="panel-heading">
                        Скан удостоверения и другие материалы
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'disabled' => false]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= (!$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model));  ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
$('input[name="TeachersQualifications[status]"]').val() === '1' ? $('.field-teachersqualifications-date').show() : $('.field-teachersqualifications-date').hide();

    $('input[name="TeachersQualifications[status]"]').click(function(){
       $(this).val() === '1' ? $('.field-teachersqualifications-date').show() : $('.field-teachersqualifications-date').hide();
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>