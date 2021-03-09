<?php

use artsoft\widgets\ActiveForm;
use common\models\own\Department;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\own\Department */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="department-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'department-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            echo $form->field($model, 'division_id')->dropDownList(\common\models\own\Division::getDivisionList(), [
                                'prompt' => Yii::t('art/guide', 'Select Name Division...'),
                                'id' => 'division_id'
                            ])->label(Yii::t('art/guide', 'Name Division'));
                            ?>

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(Department::getStatusList()) ?>

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
    </div>
</div>
