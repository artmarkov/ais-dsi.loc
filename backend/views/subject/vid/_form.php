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

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

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

                            <!--                    --><? //= $form->field($model->loadDefaultValues(), 'status')->dropDownList(SubjectVid::getStatusList()) ?>
                            <?php
                            //                     Adjust handle width for longer labels
                            echo $form->field($model->loadDefaultValues(), 'status')->widget(kartik\switchinput\SwitchInput::classname(), [
                                'pluginOptions' => [
                                    //'handleWidth' => 60,
                                    'onText' => Yii::t('art', 'Active'),
                                    'offText' => Yii::t('art', 'Inactive'),
                                    'size' => 'mini',
                                ]
                            ]);
                            ?>

                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('art', 'Go to list'), ['/subject/vid/index'], ['class' => 'btn btn-default']) ?>
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> ' . Yii::t('art', 'Delete'),
                                ['/subject/vid/delete', 'id' => $model->id], [
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

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
