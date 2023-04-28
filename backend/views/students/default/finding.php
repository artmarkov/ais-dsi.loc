<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \artsoft\auth\models\forms\FindingForm */

use artsoft\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->params['breadcrumbs'][] = 'Поиск ученика';

?>
<div class="panel">
    <div class="panel-heading">
        Поиск ученика
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'form-find',
        'options' => ['autocomplete' => 'off'],
        'validateOnBlur' => false,
        'fieldConfig' => [

        ],
    ]); ?>
    <div class="panel-body">
        <div class="row">
            <?= $form->field($model, 'last_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
            <?= $form->field($model, 'first_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
            <?= $form->field($model, 'middle_name')->textInput(['autocomplete' => 'off', 'maxlength' => 124]) ?>
            <?= $form->field($model, 'birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')]); ?>

        </div>
    </div>
    <div class="panel-footer">
        <div class="form-group btn-group">
        <?= \artsoft\helpers\Html::submitButton(
            '<i class="fa fa-search" aria-hidden="true"></i> ' . Yii::t('art', 'Finding'),
            [
                'class' => 'btn btn-primary btn-md' ,
                'name' => 'submitAction',
                'value' => 'save',
            ]
        );
           ?>
        </div>
    </div>

    <?php ActiveForm::end() ?>
</div>


