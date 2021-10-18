<?php

use artsoft\widgets\ActiveForm;
use common\models\info\Board;
use artsoft\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\info\Board */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="board-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'board-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Html::encode($this->title) ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, 'author_id')->textInput() ?>

                            <?= $form->field($model, 'category_id')->dropDownList(Board::getCategoryList()) ?>

                            <?= $form->field($model, 'importance_id')->dropDownList(Board::getImportanceList()) ?>

                            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                            <?= $form->field($model, 'recipients_list')->widget(\kartik\select2\Select2::class, [
                                'data' => Board::getRecipientsList(),
                                'options' => [
//                                    'disabled' => $readonly,
                                    'placeholder' => Yii::t('art/info', 'Select Recipients...'),
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/info', 'Recipients'));
                            ?>
                            <?= $form->field($model, 'board_date')->widget(DatePicker::class) ?>

                            <?= $form->field($model, 'delete_date')->widget(DatePicker::class) ?>

                            <?= $form->field($model, 'status')->dropDownList(Board::getStatusList()) ?>

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
