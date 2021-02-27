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

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">
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

        <div class="col-md-3">

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="record-info">
                        <div class="form-group clearfix">
                            <label class="control-label" style="float: left; padding-right: 5px;"><?= $model->attributeLabels()['id'] ?>: </label>
                            <span><?= $model->id ?></span>
                        </div>
                        
                        <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(BonusItem::getStatusList()) ?>

                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/teachers/bonus-item/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?=
                                Html::a(Yii::t('art', 'Delete'), ['/teachers/bonus-item/delete', 'id' => $model->id], [
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
