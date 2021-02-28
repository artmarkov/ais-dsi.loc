<?php

use artsoft\widgets\ActiveForm;
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
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, 'num')->textInput() ?>

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'floor')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'area')->textInput() ?>

                            <?= $form->field($model, 'capacity')->textInput() ?>

                            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

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

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/auditory/default/index'], ['class' => 'btn btn-default']) ?>
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'),
                                ['/auditory/default/delete', 'id' => $model->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]) ?>
                        <?php endif; ?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>
