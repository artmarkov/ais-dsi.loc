<?php

use artsoft\widgets\ActiveForm;
use common\models\schoolplan\Schoolplan;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\Schoolplan */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="schoolplan-plan-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'schoolplan-plan-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?=  Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
            
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'datetime_in')->textInput() ?>

                    <?= $form->field($model, 'datetime_out')->textInput() ?>

                    <?= $form->field($model, 'places')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'auditory_id')->textInput() ?>

                    <?= $form->field($model, 'department_list')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'teachers_list')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'category_id')->textInput() ?>

                    <?= $form->field($model, 'form_partic')->textInput() ?>

                    <?= $form->field($model, 'partic_price')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'visit_flag')->textInput() ?>

                    <?= $form->field($model, 'visit_content')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'important_flag')->textInput() ?>

                    <?= $form->field($model, 'region_partners')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'site_url')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'site_media')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'rider')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'result')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'num_users')->textInput() ?>

                    <?= $form->field($model, 'num_winners')->textInput() ?>

                    <?= $form->field($model, 'num_visitors')->textInput() ?>

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
