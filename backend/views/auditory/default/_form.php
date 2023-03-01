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
    $form = ActiveForm::begin()
    ?>
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'num')->textInput() ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'building_id')
                        ->dropDownList(AuditoryBuilding::getAuditoryBuildingList(), [
                            'prompt' => Yii::t('art/guide', 'Select Building...')
                        ]);
                    ?>
                    <?= $form->field($model, 'cat_id')
                        ->dropDownList(AuditoryCat::getAuditoryCatList(), [
                            'prompt' => Yii::t('art/guide', 'Select Cat...')
                        ]);
                    ?>
                    <?= $form->field($model, 'floor')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'area')->textInput() ?>

                    <?= $form->field($model, 'capacity')->textInput() ?>

                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'study_flag')->checkbox() ?>

                    <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(\common\models\auditory\Auditory::getStatusList()) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>

</div>
