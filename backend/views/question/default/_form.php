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
    function toggleQuestion(value) {
      if (value === '1'){
          $('.field-question-users_cat').show();
          $('.field-question-division_list').show();
          // $('.field-question-moderator_list').hide();
      } else {
          $('.field-question-users_cat').hide();
          $('.field-question-division_list').hide();
          // $('.field-question-moderator_list').show();
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
                            'multiple' => true,
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

                    ]); ?>

                    <?= $form->field($model, 'timestamp_in')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'timestamp_out')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'status')->dropDownList(Question::getStatusList(), ['disabled' => $readonly]) ?>

                    <?= $form->field($model, 'email_flag')->checkbox(['disabled' => $readonly])->hint('При наличии в форме E-mail, пользователь получит уведомление.') ?>

                    <?= $form->field($model, 'email_author_flag')->checkbox(['disabled' => $readonly])->hint('Автор формы получит уведомление при каждом её заполнении.') ?>

                    <?= $form->field($model, 'question_limit')->widget(kartik\touchspin\TouchSpin::class, [
                        'disabled' => $readonly,
                        'pluginOptions' => [
                            'min' => 0,
                            'max' => 1000,
                        ]])->hint('Укажите колличество заявок, опросов от 0 до 1000. 0 - колличество не ограничено.'); ?>
                </div>
            </div>
            <?php if (!$model->isNewRecord) : ?>
                <div class="panel panel-default">
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
