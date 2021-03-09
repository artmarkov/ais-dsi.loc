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
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, 'num')->textInput() ?>

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

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
                            <?= $form->field($model, 'floor')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'area')->textInput() ?>

                            <?= $form->field($model, 'capacity')->textInput() ?>

                            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group btn-group">
                        <?= \artsoft\helpers\ButtonHelper::submitButtons($model);?>
                    </div>
                    <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>
