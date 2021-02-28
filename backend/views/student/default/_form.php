<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\student\Student */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="student-form">

    <?php $form = ActiveForm::begin([
        'id' => 'student-form',
        'validateOnBlur' => false,
        'enableAjaxValidation' => true,
        'options' => ['enctype' => 'multipart/form-data']
    ]);
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

                            <?= $form->field($modelUser, 'last_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($modelUser, 'first_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($modelUser, 'middle_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($modelUser, 'gender')->dropDownList(artsoft\models\User::getGenderList()) ?>

                            <?php if ($modelUser->birth_timestamp) $modelUser->birth_timestamp = date("d-m-Y", (integer)mktime(0, 0, 0, date("m", $modelUser->birth_timestamp), date("d", $modelUser->birth_timestamp), date("Y", $modelUser->birth_timestamp))); ?>

                            <?= $form->field($modelUser, 'birth_timestamp')->widget(MaskedInput::className(), [
                                'mask' => Yii::$app->settings->get('reading.date_mask'),
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'birth_date_1'
                                ],
                                'clientOptions' => [
                                    'clearIncomplete' => true
                                ]
                            ]);
                            ?>
                            <?= $form->field($modelUser, 'snils')->widget(MaskedInput::className(), [
                                'mask' => Yii::$app->settings->get('reading.snils_mask'),
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'snils_1'
                                ],
                                'clientOptions' => [
                                    'clearIncomplete' => true
                                ]
                            ]) ?>
                            <?= $form->field($modelUser, 'phone')->widget(MaskedInput::className(), [
                                'mask' => Yii::$app->settings->get('reading.phone_mask'),
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'phone_1'
                                ],
                                'clientOptions' => [
                                    'clearIncomplete' => true
                                ]
                            ]) ?>
                            <?= $form->field($modelUser, 'phone_optional')->widget(MaskedInput::className(), [
                                'mask' => Yii::$app->settings->get('reading.phone_mask'),
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'phone_optional_1'
                                ],
                                'clientOptions' => [
                                    'clearIncomplete' => true
                                ]
                            ]) ?>

                            <?= $form->field($model, 'sertificate_name')->textInput(['maxlength' => true]) ?>

                            <?php if ($model->sertificate_timestamp) $model->sertificate_timestamp = date("d-m-Y", (integer)mktime(0, 0, 0, date("m", $model->sertificate_timestamp), date("d", $model->sertificate_timestamp), date("Y", $model->sertificate_timestamp))); ?>
                            <?= $form->field($model, 'sertificate_timestamp')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput() ?>

                            <?= $form->field($model, 'sertificate_series')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'sertificate_num')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'sertificate_organ')->textInput(['maxlength' => true]) ?>
                            <?php if (!$model->isNewRecord) : ?>
                                <?= \backend\widgets\ParentsViewWidget::widget(['model' => $model]); ?>
                            <?php endif; ?>

                            <?php
                            echo $form->field($model, 'position_id')->dropDownList(\common\models\student\StudentPosition::getPositionList(), [
                                'prompt' => Yii::t('art/student', 'Select Position...'),
                                'id' => 'position_id'
                            ])->label(Yii::t('art/student', 'Name Position'));
                            ?>

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                            <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/student/default/index'], ['class' => 'btn btn-default']) ?>
                            <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?=
                            Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'), ['/student/default/delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ])
                            ?>
                        <?php endif; ?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
