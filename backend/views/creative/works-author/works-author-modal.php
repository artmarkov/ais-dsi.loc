<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\user\UserCommon */
/* @var $form artsoft\widgets\ActiveForm */

?>

<div class="works-author-form">
    <?php if ($model->isNewRecord) {
        $action = 'create-author';
    } else {
        $action = 'update-author';
    }
    ?>
    <?php $form = ActiveForm::begin([
    'id' => 'works-author-form',
        
    'enableAjaxValidation' => true,
    'action' => ['creative/works-author/' . $action , 'id' => $model->id]
]);
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group clearfix">
                <h4><?= $modelUser->fullName ?></h4>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'weight')->textInput() ?>
                </div>
                <div class="col-md-6">
                    
                    <?php if ($model->timestamp_weight) {
                      // преобразуем на 1 число месяца
                        $model->timestamp_weight = date("m-Y", (integer) mktime(0,0,0, date("m", $model->timestamp_weight), 1, date("Y", $model->timestamp_weight))); 
                    }           
                    ?>
                    <?= $form->field($model, 'timestamp_weight')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'options' => ['placeholder' => ''],
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'MM-yyyy',
                            'minViewMode' => 1,
                            'maxViewMode' => 2,
                            'autoclose' => true,
                        ]
                    ])->textInput(['autocomplete' => 'off']); ?>
                </div>
            </div>

            <?= $form->field($model, 'works_id')->label(false)->hiddenInput() ?>
            <?= $form->field($model, 'author_id')->label(false)->hiddenInput() ?>

            <?php if ($model->isNewRecord) {
                echo Html::submitButton(Yii::t('art', 'Add'), ['class' => 'btn btn-primary']);
            } else {
                echo Html::submitButton(Yii::t('art', 'Update'), ['class' => 'btn btn-primary']);
            }
            ?>

        </div>
    </div>

<?php ActiveForm::end(); ?>

</div>
