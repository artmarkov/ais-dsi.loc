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


    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($modelUser, 'last_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($modelUser, 'first_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($modelUser, 'middle_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($modelUser, 'gender')->dropDownList(artsoft\models\User::getGenderList()) ?>
                        </div>
                        <div class="col-md-3">
                            <?php  if($modelUser->birth_timestamp) $modelUser->birth_timestamp = date("d-m-Y", (integer) mktime(0,0,0, date("m", $modelUser->birth_timestamp), date("d", $modelUser->birth_timestamp), date("Y", $modelUser->birth_timestamp)));  ?>
                            <?= $form->field($modelUser, 'birth_timestamp')->widget(MaskedInput::className(),[
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

                        </div>
                        <div class="col-md-3">
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
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
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
                        </div>
                        <div class="col-md-6">
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
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-9">
                            <?= $form->field($model, 'sertificate_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-3">
                            <?php  if($model->sertificate_timestamp) $model->sertificate_timestamp = date("d-m-Y", (integer) mktime(0,0,0, date("m", $model->sertificate_timestamp), date("d", $model->sertificate_timestamp), date("Y", $model->sertificate_timestamp)));  ?>
                            <?= $form->field($model, 'sertificate_timestamp')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'sertificate_series')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sertificate_num')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'sertificate_organ')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$model->isNewRecord) : ?>
                <?= \backend\widgets\ParentsViewWidget::widget(['model' => $model]); ?>
            <?php endif; ?>

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

                        <?php
                        echo $form->field($model, 'position_id')->dropDownList(\common\models\student\StudentPosition::getPositionList(), [
                            'prompt' => Yii::t('art/student', 'Select Position...'),
                            'id' => 'position_id'
                        ])->label(Yii::t('art/student', 'Name Position'));
                        ?>

                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/student/default/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?=
                                Html::a(Yii::t('art', 'Delete'), ['/student/default/delete', 'id' => $model->id], [
                                    'class' => 'btn btn-default',
                                    'data' => [
                                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ])
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
