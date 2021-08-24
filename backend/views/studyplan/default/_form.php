<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\studyplan\Studyplan;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\Studyplan */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="studyplan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'studyplan-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Html::encode($this->title) ?>
                    <?php if (!$model->isNewRecord): ?>
                        <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/studyplan/default/history', 'id' => $model->id]); ?></span>
                    <?php endif; ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, "student_id")->widget(\kartik\select2\Select2::class, [
                                'data' => RefBook::find('students_fullname', $model->isNewRecord ? \common\models\user\UserCommon::STATUS_ACTIVE : '')->getList(),
                                'options' => [
                                   // 'disabled' => $readonly,
                                    'placeholder' => Yii::t('art/studyplan', 'Select Student...'),
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ])->label(Yii::t('art/student', 'Student'));
                            ?>
                            <?= $form->field($model, "programm_id")->widget(\kartik\select2\Select2::class, [
                                'data' => RefBook::find('education_programm_name', $model->isNewRecord ? \common\models\education\EducationProgramm::STATUS_ACTIVE : '')->getList(),
                                'options' => [
                                    // 'disabled' => $readonly,
                                    'placeholder' => Yii::t('art/studyplan', 'Select Education Programm...'),
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ])->label(Yii::t('art/studyplan', 'Education Programm'));
                            ?>

                            <?= $form->field($model, 'course')->textInput() ?>

                            <?= $form->field($model, 'plan_year')->textInput() ?>

                            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'status')->dropDownList(Studyplan::getStatusList(), [/*'disabled' => $readonly*/]) ?>


                        </div>
                    </div>
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
