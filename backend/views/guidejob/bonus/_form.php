<?php

use artsoft\widgets\ActiveForm;
use common\models\guidejob\BonusItem;
use common\models\guidejob\BonusCategory;
use common\models\service\MeasureUnit;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\guidejob\BonusItem */
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

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
