<?php

use artsoft\widgets\ActiveForm;
use common\models\subject\SubjectCategoryItem;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\subject\SubjectCategoryItem */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="subject-category-item-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'subject-category-item-form',
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

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                            <? //= $form->field($model, 'order')->textInput() ?>

                            <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(SubjectCategoryItem::getStatusList()) ?>

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/subject/category-item/index'], ['class' => 'btn btn-default']) ?>
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?=
                            Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'), ['/subject/category-item/delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ])
                            ?>
                        <?php endif; ?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
