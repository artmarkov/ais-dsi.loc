<?php

use artsoft\widgets\ActiveForm;
use common\models\subject\Subject;
use artsoft\helpers\Html;
use nex\chosen\Chosen;

/* @var $this yii\web\View */
/* @var $model common\models\subject\Subject */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="subject-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'teachers-form',
        'validateOnBlur' => false,
        'enableAjaxValidation' => true,
        'options' => ['enctype' => 'multipart/form-data'],
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

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                            <? //= $form->field($model, 'order')->textInput() ?>
                            <?php
                            echo $form->field($model, 'department_list')->widget(Chosen::className(), [
                                'items' => Subject::getDepartmentList(),
                                'multiple' => true,
                                'placeholder' => Yii::t('art/guide', 'Select Department...'),
                            ])->label(Yii::t('art/guide', 'Department'));
                            ?>

                            <?php
                            echo $form->field($model, 'category_list')->widget(Chosen::className(), [
                                'items' => Subject::getSubjectCategoryList(),
                                'multiple' => true,
                                'placeholder' => Yii::t('art/guide', 'Select Subject Category...'),
                            ])->label(Yii::t('art/guide', 'Subject Category'));
                            ?>

                            <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(Subject::getStatusList()) ?>

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::submitButtons($model);?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
