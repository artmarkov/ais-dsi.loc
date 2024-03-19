<?php

use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use common\models\question\Question;
use artsoft\helpers\Html;
use common\models\question\QuestionOptions;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\question\Question */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $modelsQuestionAttribute common\models\question\QuestionAttribute */
/* @var $modelsQuestionOptions common\models\question\QuestionOptions */
/* @var $readonly */

$js = <<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Поле: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Поле: " + (index + 1))
    });
});


JS;

$this->registerJs($js);

$this->registerJs(<<<JS
   function toggle(index, value) {
      if(value == 7 || value == 8 || value == 77 || value == 88) {
             $('.questionForm_' + index).show();
             // $('.field-questionattribute-' + index + '-default_value').hide();
         } else {
             $('.questionForm_' + index).hide();
             // $('.field-questionattribute-' + index + '-default_value').show();
         }
    }
    jQuery(".dynamicform_wrapper .typeId").each(function(index) {
        let field = document.getElementById("questionattribute-" + index + "-type_id");
        toggle(index, field.value);
        field.addEventListener('change', (event) => {
          toggle(index, event.target.value);
        });
    });
JS
    , \yii\web\View::POS_END);

$js = <<<JS
    function toggleQuestion(value) {
      if (value === '1'){
          $('.field-question-users_cat').show();
          $('.field-question-division_list').show();
          $('.field-question-moderator_list').hide();
      } else {
          $('.field-question-users_cat').hide();
          $('.field-question-division_list').hide();
          $('.field-question-moderator_list').show();
      }
    }
    toggleQuestion($('input[name="Question[vid_id]"]:checked').val());
    $('input[name="Question[vid_id]"]').click(function(){
       toggleQuestion($(this).val());
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>

<div class="question-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'question-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка формы
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'name')->textInput() ?>

                    <?= $form->field($model->loadDefaultValues(), 'author_id')->widget(\kartik\select2\Select2::class, [
                        'data' => User::getUsersListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            //'value' => $model->author_id,
                            'placeholder' => Yii::t('art/guide', 'Select Authors...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            //'minimumInputLength' => 3,
                        ],

                    ])->hint('Укажите автора формы. При необходимости, автору будут присылаться уведомления по заполнению формы.');
                    ?>

                    <?= $form->field($model, 'category_id')->radioList(Question::getCategoryList()) ?>

                    <?= $form->field($model, 'vid_id')->radioList(Question::getVidList()) ?>

                    <?= $form->field($model, 'users_cat')->widget(Select2::className(), [
                        'data' => Question::getGroupList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'moderator_list')->widget(Select2::className(), [
                        'data' => User::getUsersListByCategory(['teachers', 'employees']),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->hint('Укажите модераторов, которые будут работать и заполнять форму.');
                    ?>

                    <?= $form->field($model, 'division_list')->widget(Select2::className(), [
                        'data' => \common\models\own\Division::getDivisionList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Division...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Division'));
                    ?>
                    <?= $form->field($model, 'description')->widget(\dosamigos\tinymce\TinyMce::className(), [
                        'options' => ['rows' => 6],
                        'language' => 'ru',

                    ]);?>

                    <?= $form->field($model, 'timestamp_in')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'timestamp_out')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'status')->dropDownList(Question::getStatusList(), ['disabled' => $readonly]) ?>

                    <?= $form->field($model, 'email_flag')->checkbox(['disabled' => $readonly]) ?>

                    <?= $form->field($model, 'email_author_flag')->checkbox(['disabled' => $readonly]) ?>
                </div>
            </div>
            <?php if (!$model->isNewRecord) : ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Загруженные материалы
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'disabled' => false]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 50, // the maximum times, an element can be added (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsQuestionAttribute[0],
                    'formId' => 'question-form',
                    'formFields' => [
                        'type_id',
                        'name',
                        'label',
                        'hint',
                        'required',
//                        'default_value',
                        'description',
                    ],
                ]); ?>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Поля формы
                    </div>
                    <div class="panel-body">
                        <div class="container-items"><!-- widgetBody -->
                            <?php foreach ($modelsQuestionAttribute as $index => $modelQuestionAttribute): ?>
                                <div class="item panel panel-info"><!-- widgetItem -->
                                    <div class="panel-heading">
                                        <span class="panel-title-activities">Поле: <?= ($index + 1) ?></span>
                                        <?php if (!$readonly): ?>
                                            <div class="pull-right">
                                                <button type="button" class="remove-item btn btn-default btn-xs">
                                                    удалить
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        // necessary for update action.
                                        if (!$modelQuestionAttribute->isNewRecord) {
                                            echo Html::activeHiddenInput($modelQuestionAttribute, "[{$index}]id");
                                        }
                                        ?>
                                        <div class="col-sm-12">
                                            <?= $form->field($modelQuestionAttribute, "[{$index}]type_id")->dropDownList(
                                                \common\models\question\QuestionAttribute::getTypeList(),
                                                [
                                                    'disabled' => $readonly,
                                                    'class' => 'form-control typeId',
                                                    'onChange' => "this.form.submit()"
                                                ]) ?>
                                            <?= $form->field($modelQuestionAttribute, "[{$index}]label")->textInput(['maxlength' => true, 'disabled' => false]) ?>

                                            <?= $form->field($modelQuestionAttribute, "[{$index}]description")->textarea(['maxlength' => true, 'disabled' => false]) ?>

                                            <?= $form->field($modelQuestionAttribute, "[{$index}]hint")->textInput(['maxlength' => true, 'disabled' => false])->hint('Так будет выглядеть подсказка поля') ?>

                                            <?= $form->field($modelQuestionAttribute, "[{$index}]required")->checkbox(['disabled' => $readonly]) ?>

                                        </div>
                                        <?= $this->render('_form-options', [
                                            'form' => $form,
                                            'index' => $index,
                                            'modelsQuestionOptions' => (empty($modelsQuestionOptions[$index])) ? [new QuestionOptions] : $modelsQuestionOptions[$index],

                                            'model' => $modelQuestionAttribute,
                                            'readonly' => $readonly,
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div><!-- .panel -->
                    <?php if (!$readonly): ?>
                        <div class="panel-footer">
                            <div class="form-group btn-group">

                                <button type="button" class="add-item btn btn-success btn-sm pull-right">
                                    <i class="glyphicon glyphicon-plus"></i> Добавить
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php DynamicFormWidget::end(); ?>
                </div>
            <?php endif; ?>
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
