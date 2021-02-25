<?php

use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var artsoft\models\AuthItemGroup $model
 * @var artsoft\widgets\ActiveForm $form
 */
?>

<div class="permission-groups-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'permission-groups-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'autofocus' => $model->isNewRecord ? true : false]) ?>
                    <?= $form->field($model, 'code')->textInput(['maxlength' => 64]) ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="record-info">
                <div class="form-group">
                        <?= Html::a(Yii::t('art', 'Go to list'), ['/user/permission-groups/index'], ['class' => 'btn btn-default']) ?>
                        <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                    <?php if (!$model->isNewRecord): ?>
                        <?= Html::a(Yii::t('art', 'Delete'), ['delete', 'id' => $model->code], [
                            'class' => 'btn btn-danger',
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

    <?php ActiveForm::end(); ?>

</div>






