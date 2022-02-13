<?php

use artsoft\widgets\ActiveForm;
use common\models\education\PieceCategory;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\PieceCategory */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="piece-category-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'piece-category-form',
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

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(PieceCategory::getStatusList()) ?>

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

<?php ActiveForm::end(); ?>

</div>
