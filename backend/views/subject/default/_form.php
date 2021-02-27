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

    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
                        </div>
                        <? //= $form->field($model, 'order')->textInput() ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?php
                    echo $form->field($model, 'department_list')->widget(Chosen::className(), [
                        'items' => Subject::getDepartmentList(),
                        'multiple' => true,
                        'placeholder' => Yii::t('art/guide', 'Select Department...'),
                    ])->label(Yii::t('art/guide', 'Department'));
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    echo $form->field($model, 'category_list')->widget(Chosen::className(), [
                        'items' => Subject::getSubjectCategoryList(),
                        'multiple' => true,
                        'placeholder' => Yii::t('art/guide', 'Select Subject Category...'),
                    ])->label(Yii::t('art/guide', 'Subject Category'));
                    ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="record-info">
                        <div class="form-group clearfix">
                            <label class="control-label"
                                   style="float: left; padding-right: 5px;"><?= $model->attributeLabels()['id'] ?>
                                : </label>
                            <span><?= $model->id ?></span>
                        </div>
                        
                        <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(Subject::getStatusList()) ?>
                        
                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/subject/default/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Delete'),
                                    ['/subject/default/delete', 'id' => $model->id], [
                                        'class' => 'btn btn-default',
                                        'data' => [
                                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
