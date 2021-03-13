<?php

use artsoft\widgets\ActiveForm;
use common\models\guidejob\Cost;
use artsoft\helpers\Html;
use common\models\guidejob\Direction;
use common\models\guidejob\Stake;

/* @var $this yii\web\View */
/* @var $model common\models\guidejob\Cost */
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
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Html::encode($this->title) ?>
                </div>
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
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model);?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
