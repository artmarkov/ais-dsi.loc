<?php

use artsoft\widgets\ActiveForm;
use common\models\question\QuestionAttribute;
use artsoft\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\question\QuestionAttribute */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $modelsQuestionOptions */

$this->registerJs(<<<JS
   function toggle(value) {
      if(value == 7 || value == 8 || value == 77 || value == 88) {
             $('.questionOptions').show();
             $('.field-questionattribute-unique_flag').hide();
         } else {
             $('.questionOptions').hide();
             $('.field-questionattribute-unique_flag').show();
         }
    }
        let field = document.getElementById("questionattribute-type_id");
        toggle(field.value);
        field.addEventListener('change', (event) => {
          toggle(event.target.value);
        });
JS
    , \yii\web\View::POS_END);
?>

<div class="question-attribute-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'question-attribute-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка поля формы
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    // necessary for update action.
                    if (!$model->isNewRecord) {
                        echo Html::activeHiddenInput($model, 'question_id');
                    }
                    ?>

                    <?= $form->field($model, 'type_id')->dropDownList(
                        \common\models\question\QuestionAttribute::getTypeList(),
                        [
                            'disabled' => $readonly,
                            'class' => 'form-control typeId',
//                            'onChange' => "this.form.submit()"
                        ]) ?>


                    <?= $form->field($model, 'label')->textInput(['maxlength' => true, 'disabled' => false]) ?>

                    <?= $form->field($model, 'description')->textarea(['maxlength' => true, 'disabled' => false]) ?>

                    <?= $form->field($model, 'hint')->textInput(['maxlength' => true, 'disabled' => false])->hint('Так будет выглядеть подсказка поля') ?>

                    <?= $form->field($model, 'required')->checkbox(['disabled' => $readonly])->hint('Поставьте галочку, чтобы сделать поле обязательным') ?>

                    <?= $form->field($model, 'unique_flag')->checkbox(['disabled' => $readonly])->hint('Будет осуществлена проверка на уникальность значения поля') ?>

                </div>
            </div>
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_inner',
                'widgetBody' => '.container-time',
                'widgetItem' => '.room-item',
                'limit' => 200,
                'min' => 1,
                'insertButton' => '.add-time',
                'deleteButton' => '.remove-time',
                'model' => $modelsQuestionOptions[0],
                'formId' => 'question-attribute-form',
                'formFields' => [
                    'name',
                    'free_flag',
                ],
            ]); ?>

            <div class="row questionOptions">
                <div class="col-sm-12">
                    <div class="col-sm-3">
                        <label class="control-label"> Опции атрибута</label>
                    </div>
                    <div class="col-sm-9">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center">
                                    <?php if (!$readonly): ?>
                                        <button type="button" class="add-time btn btn-success btn-xs"><span
                                                    class="fa fa-plus"></span></button>
                                    <?php endif; ?>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="container-time">
                            <?php foreach ($modelsQuestionOptions as $indexTime => $modelQuestionOptions): ?>
                                <tr class="room-item">
                                    <?php
                                    // necessary for update action.
                                    if (!$modelQuestionOptions->isNewRecord) {
                                        echo \yii\helpers\Html::activeHiddenInput($modelQuestionOptions, "[{$indexTime}]id");
                                    }
                                    ?>
                                    <td>
                                        <?= $form->field($modelQuestionOptions, "[{$indexTime}]name")->textInput() ?>
                                    </td>
                                    <td>
                                        <?= $form->field($modelQuestionOptions, "[{$indexTime}]free_flag")->checkbox(['disabled' => $readonly]) ?>
                                    </td>

                                    <td class="vcenter">
                                        <?php if (!$readonly): ?>
                                            <button type="button" class="remove-time btn btn-danger btn-xs"><span
                                                        class="fa fa-minus"></span></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
