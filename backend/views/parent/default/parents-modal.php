<?php

use artsoft\widgets\ActiveForm;
use common\models\user\User;
use artsoft\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\user\UserCommon */
/* @var $form artsoft\widgets\ActiveForm */

?>

<div class="parents-form">

    <?php $form = ActiveForm::begin([
    'id' => 'parent-form',
        
    'enableAjaxValidation' => true,
    'action' => ['parent/default/create-family']
]);
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 124]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($modelFamily, 'relation_id')->dropDownList(\common\models\user\UserRelation::getRelationList(), [
                                'prompt' => Yii::t('art/user','Select Relation...'),
                            ])->label(Yii::t('art/user', 'Family Relation')); ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'gender')->dropDownList(User::getGenderList()) ?>
                        </div>
                        <div class="col-md-3">
                            <?php  if($model->birth_timestamp) $model->birth_timestamp = date("d-m-Y", (integer) mktime(0,0,0, date("m", $model->birth_timestamp), date("d", $model->birth_timestamp), date("Y", $model->birth_timestamp)));  ?>
                            <?= $form->field($model, 'birth_timestamp')->widget(MaskedInput::className(),[
                                'mask' => Yii::$app->settings->get('reading.date_mask'),
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'birth_date_2'
                                ],
                                'clientOptions' => [
                                    'clearIncomplete' => true
                                ]
                            ]);
                            ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'snils')->widget(MaskedInput::className(), [
                                'mask' => Yii::$app->settings->get('reading.snils_mask'),
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'snils_2'
                                ],
                                'clientOptions' => [
                                    'clearIncomplete' => true
                                ]
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
                                'mask' => Yii::$app->settings->get('reading.phone_mask'),
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'phone_2'
                                ],
                                'clientOptions' => [
                                    'clearIncomplete' => true
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'phone_optional')->widget(MaskedInput::className(), [
                                'mask' => Yii::$app->settings->get('reading.phone_mask'),
                                'options' => [
                                    'class' => 'form-control',
                                    'id' => 'phone_optional_2'
                                ],
                                'clientOptions' => [
                                    'clearIncomplete' => true
                                ]
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <?= $form->field($modelFamily, 'user_main_id')->label(false)->hiddenInput() ?>

            <?= $form->field($modelFamily, 'user_slave_id')->label(false)->hiddenInput() ?>

            <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>


<?php ActiveForm::end(); ?>

</div>
