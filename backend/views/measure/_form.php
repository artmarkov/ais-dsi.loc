<?php

use common\widgets\ActiveForm;
use common\models\Measure;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Measure */
/* @var $form artsoft\widgets\ActiveForm */
//echo '<pre>' . print_r($model, true) . '</pre>';
//echo '<pre>' . print_r($model->getEavAttribute('field_1')) . '</pre>';

?>

<div class="measure-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'measure-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="row">
        <div class="col-md-9">

            <div class="panel panel-default">
                <div class="panel-body">
                    
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'abbr')->textInput(['maxlength' => true]) ?>

                   <?= $form->field($model, 'category_id')->textInput(['maxlength' => true]) ?>

                   <?php
                   
                       foreach ($model->getEavAttributes() as $attr) {

                       //echo '<pre>' . print_r($attr, true) . '</pre>';
                           
                       $eav_atribute_type = $model->getEavAttribute($attr)->type_id;
                       $eav_atribute_name = $model->getEavAttribute($attr)->name;
                       $eav_atribute_hint = $model->getEavAttribute($attr)->description;
                       $eav_atribute_label = $model->getEavAttribute($attr)->label;
                       $eav_option_list = $model->getEavAttribute($eav_atribute_name)->getEavOptionsList();
                       $fieldOptions = [
                            'options' => ['class' => 'form-group has-feedback'],
                            'inputTemplate' => "{input}<span class='fa fa-" . $model->getEavAttribute($attr)->icon . " form-control-feedback'></span>"
                        ];

                        switch ($eav_atribute_type) {
                           case 1:
                               echo $form->field($model, $eav_atribute_name, $fieldOptions)->textInput()->hint($eav_atribute_hint)->label($eav_atribute_label);
                               break;
                           case 2:
                               echo $form->field($model, $eav_atribute_name)->dropDownList($eav_option_list)->hint($eav_atribute_hint)->label($eav_atribute_label);
                               break;
                           case 3:
                               echo $form->field($model, $eav_atribute_name)->checkbox()->hint($eav_atribute_hint)->label($eav_atribute_label);
                               break;
                       }
                   }
                   ?>

              </div>

          </div>
      </div>

      <div class="col-md-3">

          <div class="panel panel-default">
              <div class="panel-body">
                  <div class="record-info">
                      <div class="form-group clearfix">
                          <label class="control-label" style="float: left; padding-right: 5px;"><?=  $model->attributeLabels()['id'] ?>: </label>
                          <span><?=  $model->id ?></span>
                      </div>

                      <div class="form-group">
                          <?php  if ($model->isNewRecord): ?>
                              <?= Html::submitButton(Yii::t('art', 'Create'), ['class' => 'btn btn-primary']) ?>
                              <?= Html::a(Yii::t('art', 'Cancel'), ['/measure/index'], ['class' => 'btn btn-default']) ?>
                          <?php  else: ?>
                              <?= Html::submitButton(Yii::t('art', 'Save'), ['class' => 'btn btn-primary']) ?>
                              <?= Html::a(Yii::t('art', 'Delete'),
                                  ['/measure/delete', 'id' => $model->id], [
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

  <?php  ActiveForm::end(); ?>

</div>
