<?php

use common\widgets\ActiveForm;
use common\models\Auditory;
use artsoft\helpers\Html;
use common\models\auditory\AuditoryBuilding;
use common\models\auditory\AuditoryCat;

/* @var $this yii\web\View */
/* @var $model common\models\Auditory */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="auditory-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'auditory-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">


                    <?= $form->field($model, 'num')->textInput() ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'floor')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'area')->textInput() ?>

                    <?= $form->field($model, 'capacity')->textInput() ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>


                </div>

            </div>
        </div>

        <div class="col-md-3">

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="record-info">
                        <div class="form-group clearfix">
                            <label class="control-label"
                                   style="float: left; padding-right: 5px;"><?= $model->attributeLabels()['id'] ?>
                                : </label>
                            <span><?= $model->id ?></span>

                        </div>
                        <?= $form->field($model, 'building_id')
                            ->dropDownList(AuditoryBuilding::getAuditoryBuildingList(), [
                                'prompt' => Yii::t('art/guide', 'Select Building...')
                            ])->label(Yii::t('art/guide', 'Name Building'));
                        ?>

                        <?= $form->field($model, 'cat_id')
                            ->dropDownList(AuditoryCat::getAuditoryCatList(), [
                                'prompt' => Yii::t('art/guide', 'Select Cat...')
                            ])->label(Yii::t('art/guide', 'Name Auditory Category'));
                        ?>

                        <?= $form->field($model, 'order')->textInput() ?>

                        <?= $form->field($model, 'study_flag')->checkbox() ?>

                        <div class="form-group">
                            <?php if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/auditory/default/index'], ['class' => 'btn btn-default']) ?>
                            <?php else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Delete'),
                                    ['/auditory/default/delete', 'id' => $model->id], [
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

    <?php ActiveForm::end(); ?>

</div>
