<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\education\LessonItems;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonItems */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $modelsItems artsoft\widgets\$modelsItems */
/* @var $studyplan_subject_id */
/* @var $subject_sect_studyplan_id */

//$this->registerJs(<<<JS
//$( ".add-item" ).click(function(){ // задаем функцию при нажатиии на элемент <button>
//	    $( "#lesson-items-form" ).submit(); // вызываем событие submit на элементе <form>
//	  });
//JS
//    , \yii\web\View::POS_END);

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
    , \yii\web\View::POS_END);

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html((index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
';

$this->registerJs($js);
?>

<div class="lesson-items-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'lesson-items-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Посещаемость и успеваемость:
                                        <?php echo RefBook::find('subject_memo_2')->getValue($model->studyplan_subject_id); ?>
                                        <?php echo RefBook::find('sect_name_2')->getValue($model->subject_sect_studyplan_id); ?>
                    <?php
                    if($model->subject_sect_studyplan_id != 0) {
                        $m = \common\models\subjectsect\SubjectSectStudyplan::findOne($model->subject_sect_studyplan_id);
                        $studyplan_list = explode(',', $m->studyplan_subject_list);
                    }
                    else{
                        $studyplan_list = [$model->studyplan_subject_id];
                    }
                    $data = [];
                    foreach ($studyplan_list as $item => $studyplan_subject_id) {
                        $student_id = RefBook::find('studyplan_subject-student')->getValue($studyplan_subject_id);
                        $data[$studyplan_subject_id] = RefBook::find('students_fio')->getValue($student_id);
                    }
                    ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?= $form->field($model, "lesson_test_id")->widget(\kartik\select2\Select2::class, [
                            'data' => \common\models\education\LessonTest::getLessonTestList($model),
                            'options' => [
//                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ]
                        ]);
                        ?>
                        <?= $form->field($model, 'lesson_date')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, [/*'disabled' => $readonly*/]); ?>
                        <?= $form->field($model, 'lesson_topic')->textInput() ?>
                        <?= $form->field($model, 'lesson_rem')->textInput() ?>

                    </div>
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item', // required: css class
                        'limit' => 999, // the maximum times, an element can be added (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $modelsItems[0],
                        'formId' => 'lesson-items-form',
                        'formFields' => [
                            'studyplan_subject_id',
                            'lesson_mark_id',
                            'mark_rem',
                        ],
                    ]); ?>

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Список оценок
                        </div>
                        <div class="panel-body">

                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center">№</th>
                                    <th class="text-center">Ученик</th>
                                    <th class="text-center">Оценка</th>
                                    <th class="text-center">Примечание</th>
                                    <th class="text-center">
                                        <!--                                                --><?php //if (!$readonly): ?>
                                        <button type="button" class="add-item btn btn-success btn-xs"><span
                                                    class="fa fa-plus"></span></button>
                                        <!--                                                --><?php //endif; ?>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="container-items">
                                <?php foreach ($modelsItems as $index => $modelItems): ?>
                                    <tr class="item">
                                        <?php
                                        // necessary for update action.
                                        if (!$modelItems->isNewRecord) {
                                            echo Html::activeHiddenInput($modelItems, "[{$index}]id");
                                        }
                                        ?>
                                        <td>
                                            <span class="panel-title-activities"><?= ($index + 1) ?></span>
                                        </td>

                                        <td>
                                            <?php
                                            $field = $form->field($modelItems, "[{$index}]studyplan_subject_id");
                                            echo $field->begin();
                                            ?>
                                            <div class="col-sm-12">
                                                <?= \kartik\select2\Select2::widget(
                                                    [
                                                        'model' => $modelItems,
                                                        'attribute' => "[{$index}]studyplan_subject_id",
                                                        'data' => $data,
                                                        'options' => [

//                                                                'disabled' => $readonly,
                                                            'placeholder' => Yii::t('art', 'Select...'),
                                                        ],
                                                        'pluginOptions' => [
                                                            'allowClear' => true
                                                        ],
                                                    ]
                                                ) ?>
                                                <p class="help-block help-block-error"></p>
                                            </div>
                                            <?= $field->end(); ?>
                                        </td>

                                        <td>
                                            <?php
                                            $field = $form->field($modelItems, "[{$index}]lesson_mark_id");
                                            echo $field->begin();
                                            ?>
                                            <div class="col-sm-12">
                                                <?= \kartik\select2\Select2::widget(
                                                    [
                                                        'model' => $modelItems,
                                                        'attribute' => "[{$index}]lesson_mark_id",
                                                        'data' => RefBook::find('lesson_mark')->getList(),
                                                        'options' => [

//                                                                'disabled' => $readonly,
                                                            'placeholder' => Yii::t('art', 'Select...'),
                                                        ],
                                                        'pluginOptions' => [
                                                            'allowClear' => true
                                                        ],
                                                    ]
                                                ) ?>
                                                <p class="help-block help-block-error"></p>
                                            </div>
                                            <?= $field->end(); ?>
                                        </td>

                                        <td>
                                            <?php
                                            $field = $form->field($modelItems, "[{$index}]mark_rem");
                                            echo $field->begin();
                                            ?>
                                            <div class="col-sm-12">
                                                <?= \yii\helpers\Html::activeTextInput($modelItems, "[{$index}]mark_rem", ['class' => 'form-control']); ?>
                                                <p class="help-block help-block-error"></p>
                                            </div>
                                            <?= $field->end(); ?>
                                        </td>

                                        <td class="vcenter text-center">
                                            <!--                                                --><?php //if (!$readonly): ?>
                                            <button type="button"
                                                    class="remove-item btn btn-danger btn-xs"><span
                                                        class="fa fa-minus"></span></button>
                                            <!--                                                --><?php //endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php DynamicFormWidget::end(); ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


