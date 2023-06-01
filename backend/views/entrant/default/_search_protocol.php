<?php

use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
/* @var $model */
?>

    <div class="entrant-result-search">
        <?php
        $form = ActiveForm::begin([
            'id' => 'entrant-result-search',
            'validateOnBlur' => false,
        ])
        ?>
        <div class="panel">
            <div class="panel-heading">
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model_date, 'members_id')->widget(\kartik\select2\Select2::className(), [
                            'data' => ['0' => '-- Все члены комисии --'] + $model->getEntrantMembersList(),
                            'options' => [
//                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(Yii::t('art/guide', 'Members Item'));
                        ?>
                        <?= $form->field($model_date, 'prep_flag')->radioList(\common\models\entrant\EntrantGroup::getPrepList())->label(Yii::t('art/guide', 'Prep Flag')); ?>
                        <?= $form->field($model_date, 'free_flag')->checkbox()->label('Чистый бланк (очистить данные)'); ?>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <!--                        Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); -->
                <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>

            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

<?php
$js = <<<JS
$('#dynamicmodel-members_id').val() !== '0' ? $('.field-dynamicmodel-free_flag').show() : $('.field-dynamicmodel-free_flag').hide();
$('#dynamicmodel-members_id').on('select2:select', function () {
    // console.log($(this).val());
     $(this).val() !== '0' ? $('.field-dynamicmodel-free_flag').show() : $('.field-dynamicmodel-free_flag').hide();
});
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>