<?php

use artsoft\widgets\ActiveForm;
use common\models\subject\SubjectVid;
use artsoft\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\subject\SubjectVid */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="subject-vid-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'subject-vid-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">
                    
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
                   
                    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

                </div>

            </div>
        </div>

        <div class="col-md-3">

            <div class="panel panel-default">
                <div class="panel-body">
                    <!--<div class="record-info">-->
                        <div class="form-group clearfix">
                            <label class="control-label" style="float: left; padding-right: 5px;"><?=  $model->attributeLabels()['id'] ?>: </label>
                            <span><?=  $model->id ?></span>
                        </div>

                     <?= $form->field($model, 'qty_min')->widget(kartik\touchspin\TouchSpin::classname(), [
                        'pluginOptions' => [
                            'buttonup_class' => 'btn btn-primary',
                            'buttondown_class' => 'btn btn-info',
                            'buttonup_txt' => '<i class="glyphicon glyphicon-plus-sign"></i>',
                            'buttondown_txt' => '<i class="glyphicon glyphicon-minus-sign"></i>'
                        ],
                    ]);
                    ?>

                     <?= $form->field($model, 'qty_max')->widget(kartik\touchspin\TouchSpin::classname(), [
                        'pluginOptions' => [
                            'buttonup_class' => 'btn btn-primary',
                            'buttondown_class' => 'btn btn-info',
                            'buttonup_txt' => '<i class="glyphicon glyphicon-plus-sign"></i>',
                            'buttondown_txt' => '<i class="glyphicon glyphicon-minus-sign"></i>'
                        ],
                    ]);
                    ?>
   
                        <?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(SubjectVid::getStatusList()) ?>
                        <?php 
   // Adjust handle width for longer labels
//                        echo $form->field($model->loadDefaultValues(), 'status')->widget(kartik\switchinput\SwitchInput::classname(), [                            
//                            'pluginOptions' => [
//                                //'handleWidth' => 60,
//                                'onText' => Yii::t('art', 'Active'),
//                                'offText' => Yii::t('art', 'Inactive'),
//                                'size' => 'mini',
//                            ]
//                        ]);
//                        ?>
                        <div class="form-group">
                            <?php  if ($model->isNewRecord): ?>
                                <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Cancel'), ['/subject/vid/index'], ['class' => 'btn btn-default']) ?>
                            <?php  else: ?>
                                <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                                <?= Html::a(Yii::t('art', 'Delete'),
                                    ['/subject/vid/delete', 'id' => $model->id], [
                                    'class' => 'btn btn-default',
                                    'data' => [
                                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        <!--</div>-->
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>
