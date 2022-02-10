<?php

use artsoft\widgets\ActiveForm;
use common\models\studyplan\StudyplanThematic;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\StudyplanThematic */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="studyplan-thematic-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'studyplan-thematic-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?=  Html::encode($this->title) ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                    
                    <?= $form->field($model, 'subject_sect_studyplan_id')->textInput() ?>

                    <?= $form->field($model, 'studyplan_subject_id')->textInput() ?>

                    <?= $form->field($model, 'thematic_category')->textInput() ?>

                    <?= $form->field($model, 'period_in')->textInput() ?>

                    <?= $form->field($model, 'period_out')->textInput() ?>

                    <?= $form->field($model, 'template_flag')->textInput() ?>

                    <?= $form->field($model, 'template_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'created_at')->textInput() ?>

                    <?= $form->field($model, 'created_by')->textInput() ?>

                    <?= $form->field($model, 'updated_at')->textInput() ?>

                    <?= $form->field($model, 'updated_by')->textInput() ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?=  \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?=  \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>
