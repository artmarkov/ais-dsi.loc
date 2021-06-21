<?php

use artsoft\widgets\ActiveForm;
use common\models\subject\SubjectVid;
use artsoft\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\subject\SubjectVid */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="subject-vid-form">

    <?php
    $form = ActiveForm::begin();
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'qty_min')->widget(kartik\touchspin\TouchSpin::classname(), [
                        'pluginOptions' => [
                            'buttonup_class' => 'btn btn-danger',
                            'buttondown_class' => 'btn btn-info',
                            'buttonup_txt' => '<i class="fa fa-plus" aria-hidden="true"></i>',
                            'buttondown_txt' => '<i class="fa fa-minus" aria-hidden="true"></i>'
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'qty_max')->widget(kartik\touchspin\TouchSpin::classname(), [
                        'pluginOptions' => [
                            'buttonup_class' => 'btn btn-danger',
                            'buttondown_class' => 'btn btn-info',
                            'buttonup_txt' => '<i class="fa fa-plus" aria-hidden="true"></i>',
                            'buttondown_txt' => '<i class="fa fa-minus" aria-hidden="true"></i>'
                        ],
                    ]);
                    ?>

                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(SubjectVid::getStatusList()) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
