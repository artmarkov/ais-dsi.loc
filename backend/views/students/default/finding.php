<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model common\models\forms\FindingForm */

use artsoft\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->params['breadcrumbs'][] = 'Поиск ученика';

?>
<div class="panel">
    <div class="panel-heading">
        Поиск ученика (Введите данные ребенка)
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
            <?= $form->field($model, 'full_name')->textInput(['autocomplete' => 'off', 'maxlength' => 512])->hint('Введите Фамилию Имя Отчество ребенка. Если Отчества нет, то вводите только Фамилию Имя') ?>
            <?= $form->field($model, 'birth_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->hint('Введите Дату рождения ребенка.') ?>

        </div>
    </div>
    <div class="panel-footer">
        <?php if (Yii::$app->user->isGuest): ?>
            <?= \yii\bootstrap\Alert::widget([
                'body' => '<i class="fa fa-info-circle"></i> Нажимая кнопку "Поиск" Вы соглашаетесь на обработку персональных данных.',
                'options' => ['class' => 'alert-info'],
            ]);?>
        <?php endif; ?>
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


