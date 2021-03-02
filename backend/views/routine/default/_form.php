<?php

use artsoft\widgets\ActiveForm;
use common\models\routine\Routine;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\routine\Routine */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="routine-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'routine-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?=  Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'color')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'cat_id')->textInput() ?>

                            <?= $form->field($model, 'start_date')->textInput() ?>

                            <?= $form->field($model, 'end_date')->textInput() ?>

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/routine/default/index'], ['class' => 'btn btn-default']) ?>
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?php  if (!$model->isNewRecord): ?>
                            <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'), ['/routine/default/delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ])
                            ?>
                        <?php endif; ?>
                    </div>
                    <?=  \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>
