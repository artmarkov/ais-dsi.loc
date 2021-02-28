<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\Cost;
use artsoft\helpers\Html;
use common\models\teachers\Direction;
use common\models\teachers\Stake;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Cost */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="cost-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'cost-form',
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
                            <?php echo $form->field($model, 'direction_id')->dropDownList(Direction::getDirectionList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Direction...'),
                                'id' => 'direction_id'
                            ])->label(Yii::t('art/teachers', 'Name Direction'));
                            ?>

                            <?php echo $form->field($model, 'stake_id')->dropDownList(Stake::getStakeList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Stake...'),
                                'id' => 'stake_id'
                            ])->label(Yii::t('art/teachers', 'Name Stake'));
                            ?>

                            <?= $form->field($model, 'stake_value')->textInput() ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/teachers/cost/index'], ['class' => 'btn btn-default']) ?>
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'),
                                ['/teachers/cost/delete', 'id' => $model->id], [
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

    <?php ActiveForm::end(); ?>

</div>
