<?php

use artsoft\widgets\ActiveForm;
use common\models\education\LessonMark;
use common\models\education\LessonTest;
use artsoft\helpers\Html;
use common\models\own\Department;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonTest */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="lesson-test-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'lesson-test-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'division_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => \common\models\own\Division::getDivisionList(),
                        'options' => [
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Division...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Division'));
                    ?>
                    <?= $form->field($model, 'test_category')->dropDownList(LessonTest::getTestCatogoryList()) ?>

                    <?= $form->field($model, 'test_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'test_name_short')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'plan_flag')->checkbox() ?>

                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(LessonTest::getStatusList()) ?>

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
