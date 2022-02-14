<?php

use artsoft\widgets\ActiveForm;
use common\models\education\LessonMark;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonMark */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="lesson-mark-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'lesson-mark-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'mark_label')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'mark_hint')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'mark_value')->textInput() ?>

                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(LessonMark::getStatusList()) ?>


                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
