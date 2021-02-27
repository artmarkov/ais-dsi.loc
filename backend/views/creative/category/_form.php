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

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">
                    
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                </div>

            </div>
        </div>

        <div class="col-md-3">

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="record-info">
                        <div class="form-group clearfix">
                            <label class="control-label" style="float: left; padding-right: 5px;"><?=  $model->attributeLabels()['id'] ?>: </label>
                            <span><?=  $model->id ?></span>
                        </div>

                        <div class="form-group">
                            <?php  if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/creative/category/index'], ['class' => 'btn btn-default']) ?>
                            <?php  else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Delete'),
                                    ['/creative/category/delete', 'id' => $model->id], [
                                    'class' => 'btn btn-default',
                                    'data' => [
                                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>
