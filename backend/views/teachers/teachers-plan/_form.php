<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\TeachersPlan;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\TeachersPlan */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="teachers-plan-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'teachers-plan-form',
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
                    
                    <?= $form->field($model, 'direction_id')->textInput() ?>

                    <?= $form->field($model, 'teachers_id')->textInput() ?>

                    <?= $form->field($model, 'plan_year')->textInput() ?>

                    <?= $form->field($model, 'week_num')->textInput() ?>

                    <?= $form->field($model, 'week_day')->textInput() ?>

                    <?= $form->field($model, 'time_plan_in')->textInput() ?>

                    <?= $form->field($model, 'time_plan_out')->textInput() ?>

                    <?= $form->field($model, 'auditory_id')->textInput() ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'created_at')->textInput() ?>

                    <?= $form->field($model, 'updated_at')->textInput() ?>

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
