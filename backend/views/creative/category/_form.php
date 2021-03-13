<?php

use artsoft\widgets\ActiveForm;
use common\models\creative\CreativeCategory;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeCategory */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="creative-category-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'creative-category-form',
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

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model);?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
