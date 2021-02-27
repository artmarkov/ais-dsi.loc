<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use nex\chosen\Chosen;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\teachers\BonusItem;
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
                            <?= $form->field($modelUser, 'birth_timestamp')->widget(MaskedInput::className(),['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname());
                            ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($modelUser, 'snils')->widget(MaskedInput::className(),['mask' => Yii::$app->settings->get('reading.snils_mask')])->textInput() ?>
                        </div>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelUser, 'phone')->widget(MaskedInput::className(),['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelUser, 'phone_optional')->widget(MaskedInput::className(),['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                        </div>
                    </div>
                    
             
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            echo $form->field($model, 'direction_id_main')->dropDownList(\common\models\teachers\Direction::getDirectionList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Direction...'),
                                'id' => 'direction_id_main'
                            ])->label(Yii::t('art/teachers', 'Name Direction Main'));
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            echo $form->field($model, 'stake_id_main')->widget(\kartik\depdrop\DepDrop::classname(), [
                                'data' => \common\models\teachers\Stake::getStakeByName($model->direction_id_main),
                                'options' => ['prompt' => Yii::t('art/teachers', 'Select Stake...'), 'id' => 'stake_id_main'],
                                'pluginOptions' => [
                                    'depends' => ['direction_id_main'],
                                    'placeholder' => Yii::t('art/teachers', 'Select Stake...'),
                                    'url' => Url::to(['/teachers/default/stake'])
                                ]
                            ])->label(Yii::t('art/teachers', 'Name Stake Main'));

                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            echo $form->field($model, 'direction_id_optional')->dropDownList(\common\models\teachers\Direction::getDirectionList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Direction...'),
                                'id' => 'direction_id_optional'
                            ])->label(Yii::t('art/teachers', 'Name Direction Optional'));
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            echo $form->field($model, 'stake_id_optional')->widget(\kartik\depdrop\DepDrop::classname(), [
                                'data' => \common\models\teachers\Stake::getStakeByName($model->direction_id_optional),
                                'options' => ['prompt' => Yii::t('art/teachers', 'Select Stake...'), 'id' => 'stake_id_optional'],
                                'pluginOptions' => [
                                    'depends' => ['direction_id_optional'],
                                    'placeholder' => Yii::t('art/teachers', 'Select Stake...'),
                                    'url' => Url::to(['/teachers/default/stake'])
                                ]
                            ])->label(Yii::t('art/teachers', 'Name Stake Optional'));

                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'year_serv')->textInput() ?>
                        </div>
                        <div class="col-md-3">
                            <?php
                            echo $form->field($model, 'time_serv_init')->widget(DatePicker::classname())->label(Yii::t('art/teachers', 'For date'));
                            ?>
                        </div>

                        <div class="col-md-3">
                            <?= $form->field($model, 'year_serv_spec')->textInput() ?>
                        </div>
                        <div class="col-md-3">
                            <?php
                            echo $form->field($model, 'time_serv_spec_init')->widget(DatePicker::classname())->label(Yii::t('art/teachers', 'For date'));
                            ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php
                    echo $form->field($model, 'department_list')->widget(Chosen::className(), [
                        'items' => Teachers::getDepartmentList(),
                        'multiple' => true,
                        'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                    ])->label(Yii::t('art/guide', 'Department'));
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    echo $form->field($model, 'bonus_list')->widget(Chosen::className(), [
                        'items' => Teachers::getBonusItemList(),
                        'multiple' => true,
                        'placeholder' => Yii::t('art/teachers', 'Select Teachers Bonus...'),
                    ])->label(Yii::t('art/teachers', 'Teachers Bonus'));
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
                        <?php
                        echo $form->field($model, 'position_id')->dropDownList(common\models\teachers\Position::getPositionList(), [
                            'prompt' => Yii::t('art/teachers', 'Select Position...'),
                            'id' => 'position_id'
                        ])->label(Yii::t('art/teachers', 'Name Position'));
                        ?>

                        <?php
                        echo $form->field($model, 'work_id')->dropDownList(common\models\teachers\Work::getWorkList(), [
                            'prompt' => Yii::t('art/teachers', 'Select Work...'),
                            'id' => 'work_id'
                        ])->label(Yii::t('art/teachers', 'Name Work'));
                        ?>

                        <?php
                        echo $form->field($model, 'level_id')->dropDownList(common\models\teachers\Level::getLevelList(), [
                            'prompt' => Yii::t('art/teachers', 'Select Level...'),
                            'id' => 'level_id'
                        ])->label(Yii::t('art/teachers', 'Name Level'));
                        ?>
                        <?= $form->field($model, 'tab_num')->textInput(['maxlength' => true]) ?>

                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/teachers/default/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?=
                                Html::a(Yii::t('art', 'Delete'), ['/teachers/default/delete', 'id' => $model->id], [
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
