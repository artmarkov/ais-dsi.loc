<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\service\Catalog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="catalog-form">
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                    $form = ActiveForm::begin([
                                'action' => $model->isNewRecord ? Url::toRoute(['/service/catalog/create']) :
                                        Url::toRoute(['/service/catalog/update', 'id' => $model->id])
                    ]);
                    ?>

                    <? //= $form->field($model, 'tree')->dropDownList([1])  ?>

                    <? //= $form->field($model, 'tree')->dropDownList(['корень'])  ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'text')->textarea() ?>


                    <div class="form-group">
                        <?php if ($model->isNewRecord): ?>
                            <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                            <?= Html::a(Yii::t('art', 'Cancel'), ['/service/catalog/index'], ['class' => 'btn btn-default']) ?>
                        <?php else: ?>
                            <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                            <?=
                            Html::a(Yii::t('art', 'Delete'), ['/service/catalog/delete', 'id' => $model->id], [
                                'class' => 'btn btn-default',
                                'data' => [
                                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ])
                            ?>
                    <?php endif; ?>
                    </div>
                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>
