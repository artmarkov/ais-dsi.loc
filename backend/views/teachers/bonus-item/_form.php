<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\BonusItem;
use common\models\teachers\BonusCategory;
use common\models\service\MeasureUnit;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\BonusItem */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="bonus-item-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'bonus-item-form',
        'validateOnBlur' => false,
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
                            <?php
                            echo $form->field($model, 'bonus_category_id')->dropDownList(BonusCategory::getBonusCategoryList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Bonus Category...'),
                                'id' => 'bonus_category_id'
                            ])->label(Yii::t('art/teachers', 'Bonus Category'));
                            ?>

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'value_default')->textInput(['maxlength' => true]) ?>

                            <?php
                            echo $form->field($model, 'measure_id')->dropDownList(MeasureUnit::getMeasureUnitList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Measure Unit...'),
                                'id' => 'measure_id'
                            ])->label(Yii::t('art/teachers', 'Measure Unit'));
                            ?>

                            <?= $form->field($model, 'bonus_rule_id')->textInput() ?>

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/teachers/bonus-item/index'], ['class' => 'btn btn-default']) ?>
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'),
                                ['/teachers/bonus-item/delete', 'id' => $model->id], [
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
