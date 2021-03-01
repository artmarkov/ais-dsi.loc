<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use nex\chosen\Chosen;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\guidejob\BonusItem;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Teachers */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="teachers-form">

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
                            <?= $form->field($modelUser, 'last_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($modelUser, 'first_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($modelUser, 'middle_name')->textInput(['maxlength' => 124]) ?>

                            <?= $form->field($modelUser, 'gender')->dropDownList(artsoft\models\User::getGenderList()) ?>

                            <?php if ($modelUser->birth_timestamp) $modelUser->birth_timestamp = date("d-m-Y", (integer)mktime(0, 0, 0, date("m", $modelUser->birth_timestamp), date("d", $modelUser->birth_timestamp), date("Y", $modelUser->birth_timestamp))); ?>
                            <?= $form->field($modelUser, 'birth_timestamp')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname());
                            ?>

                            <?= $form->field($modelUser, 'snils')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput() ?>

                            <?= $form->field($modelUser, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>

                            <?= $form->field($modelUser, 'phone_optional')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>

                            <?php
                            echo $form->field($model, 'direction_id_main')->dropDownList(\common\models\guidejob\Direction::getDirectionList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Direction...'),
                                'id' => 'direction_id_main'
                            ])->label(Yii::t('art/teachers', 'Name Direction Main'));
                            ?>
                            <?php
                            echo $form->field($model, 'stake_id_main')->widget(\kartik\depdrop\DepDrop::classname(), [
                                'data' => \common\models\guidejob\Stake::getStakeByName($model->direction_id_main),
                                'options' => ['prompt' => Yii::t('art/teachers', 'Select Stake...'), 'id' => 'stake_id_main'],
                                'pluginOptions' => [
                                    'depends' => ['direction_id_main'],
                                    'placeholder' => Yii::t('art/teachers', 'Select Stake...'),
                                    'url' => Url::to(['/teachers/default/stake'])
                                ]
                            ])->label(Yii::t('art/teachers', 'Name Stake Main'));

                            ?>
                            <?php
                            echo $form->field($model, 'direction_id_optional')->dropDownList(\common\models\guidejob\Direction::getDirectionList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Direction...'),
                                'id' => 'direction_id_optional'
                            ])->label(Yii::t('art/teachers', 'Name Direction Optional'));
                            ?>
                            <?php
                            echo $form->field($model, 'stake_id_optional')->widget(\kartik\depdrop\DepDrop::classname(), [
                                'data' => \common\models\guidejob\Stake::getStakeByName($model->direction_id_optional),
                                'options' => ['prompt' => Yii::t('art/teachers', 'Select Stake...'), 'id' => 'stake_id_optional'],
                                'pluginOptions' => [
                                    'depends' => ['direction_id_optional'],
                                    'placeholder' => Yii::t('art/teachers', 'Select Stake...'),
                                    'url' => Url::to(['/teachers/default/stake'])
                                ]
                            ])->label(Yii::t('art/teachers', 'Name Stake Optional'));

                            ?>

                            <?= $form->field($model, 'year_serv')->textInput() ?>

                            <?php
                            echo $form->field($model, 'time_serv_init')->widget(DatePicker::classname())->label(Yii::t('art/teachers', 'For date'));
                            ?>

                            <?= $form->field($model, 'year_serv_spec')->textInput() ?>

                            <?php
                            echo $form->field($model, 'time_serv_spec_init')->widget(DatePicker::classname())->label(Yii::t('art/teachers', 'For date'));
                            ?>

                            <?php
                            echo $form->field($model, 'department_list')->widget(Chosen::className(), [
                                'items' => Teachers::getDepartmentList(),
                                'multiple' => true,
                                'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                            ])->label(Yii::t('art/guide', 'Department'));
                            ?>
                            <?php
                            echo $form->field($model, 'bonus_list')->widget(Chosen::className(), [
                                'items' => Teachers::getBonusItemList(),
                                'multiple' => true,
                                'placeholder' => Yii::t('art/teachers', 'Select Teachers Bonus...'),
                            ])->label(Yii::t('art/teachers', 'Teachers Bonus'));
                            ?>

                            <?php
                            echo $form->field($model, 'position_id')->dropDownList(common\models\guidejob\Position::getPositionList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Position...'),
                                'id' => 'position_id'
                            ])->label(Yii::t('art/teachers', 'Name Position'));
                            ?>

                            <?php
                            echo $form->field($model, 'work_id')->dropDownList(common\models\guidejob\Work::getWorkList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Work...'),
                                'id' => 'work_id'
                            ])->label(Yii::t('art/teachers', 'Name Work'));
                            ?>

                            <?php
                            echo $form->field($model, 'level_id')->dropDownList(common\models\guidejob\Level::getLevelList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Level...'),
                                'id' => 'level_id'
                            ])->label(Yii::t('art/teachers', 'Name Level'));
                            ?>

                            <?= $form->field($model, 'tab_num')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/teachers/default/index'], ['class' => 'btn btn-default']) ?>
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'),
                                ['/teachers/default/delete', 'id' => $model->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]) ?>
                        <?php endif; ?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
