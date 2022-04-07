<?php

use common\models\user\UserCommon;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\info\Document */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="document-form">
    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'document-form',
        'validateOnBlur' => false,
    ]);
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка документа
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    if ('info/document' != Yii::$app->controller->id) {
                        echo Html::activeHiddenInput($model, "user_common_id");
                    } else {
                        echo  $form->field($model->loadDefaultValues(), 'user_common_id')->widget(\kartik\select2\Select2::class, [
                            'data' => UserCommon::getUsersCommonListByCategory(['teachers', 'employees','students','parents']),
                            'showToggleAll' => false,
                            'options' => [
                            'disabled' => $readonly,
                                'value' =>  $model->user_common_id,
                                'placeholder' => Yii::t('art/guide', 'Select ...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                                'minimumInputLength' => 3,
                            ],

                        ]);
                    }
                    ?>

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'doc_date')->widget(DatePicker::class, [
                        'options' => [
                            'value' => $model->isNewRecord ? date('d.m.Y') : $model->doc_date
                        ]
                    ]); ?>

                </div>
            </div>
            <?php
            if (!$model->isNewRecord):
                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'disabled' => $readonly]) ?>
                    </div>
                </div>
            <?php
            endif;
            ?>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
