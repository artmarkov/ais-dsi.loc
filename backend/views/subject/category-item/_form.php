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

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                    <?//= $form->field($model, 'order')->textInput() ?>

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

                        <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(SubjectCategoryItem::getStatusList()) ?>

                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/subject/category-item/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?=
                                Html::a(Yii::t('art', 'Delete'), ['/subject/category-item/delete', 'id' => $model->id], [
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
