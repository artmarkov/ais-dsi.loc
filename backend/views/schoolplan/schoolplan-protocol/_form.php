<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanProtocol */
/* @var $modelsProtocolItems common\models\schoolplan\SchoolplanProtocolItems */
/* @var $form artsoft\widgets\ActiveForm */

/*$this->registerJs(<<<JS
$( ".add-item" ).click(function(){ // задаем функцию при нажатиии на элемент <button>
	    $( "#schoolplan-protocol-form" ).submit(); // вызываем событие submit на элементе <form>
	  });
JS
    , \yii\web\View::POS_END);*/

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
    , \yii\web\View::POS_END);

$js = <<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Учащийся: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Учащийся: " + (index + 1))
    });
});


JS;

$this->registerJs($js);
?>

<?php
$readonlyItems = \artsoft\Art::isFrontend() ? $model->protocolIsAvailable() : false;
//$readonly = User::hasRole(['teacher']) ? true : $readonly;
//$this->registerJs(<<<JS
//   function toggle(index, field) {
//      if($(field).is(':checked')) {
//             $('.markForm_' + index).show();
//         } else {
//             $('.markForm_' + index).hide();
//         }
//    }
//    jQuery(".dynamicform_wrapper .typeId").each(function(index) {
//        let field = document.getElementById("schoolplanprotocolitems-" + index + "-mark_flag");
//              console.log(index);
//        toggle(index, field);
//        field.addEventListener('change', (event) => {
//          toggle(index, event.target);
//        });
//    });
//JS
//    , \yii\web\View::POS_END);

?>
<div class="schoolplan-protocol-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'schoolplan-protocol-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка протокола мероприятия
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    if ($model->isNewRecord) {
                        echo Html::activeHiddenInput($model, "schoolplan_id");
                    }
                    ?>

                    <?= $form->field($model, 'protocol_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'protocol_date')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true]) ?>

                    <?= $form->field($model, 'leader_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'value' => $model->leader_id,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);

                    ?>
                    <?= $form->field($model, 'secretary_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'value' => $model->secretary_id,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);

                    ?>
                    <?= $form->field($model, 'members_list')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\models\User::getUsersListByCategory(['teachers']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);

                    ?>
                    <?= $form->field($model, 'subject_list')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\RefBook::find('subject_name')->getList(),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);

                    ?>

                </div>
            </div>
            <?php if (!$model->isNewRecord): ?>
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 999, // the maximum times, an element can be added (default 999)
                'min' => 0, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsProtocolItems[0],
                'formId' => 'schoolplan-protocol-form',
                'formFields' => [
                    'schoolplan_protocol_id',
                    'studyplan_subject_id',
                    'thematic_items_list',
                    'lesson_mark_id',
                    'winner_id',
                    'resume',
                    'status_exe',
                    'status_sign',
                    'signer_id',
                ],
            ]); ?>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Учащиеся
                            </div>
                            <div class="panel-body">
                                <div class="container-items"><!-- widgetBody -->
                                    <?php foreach ($modelsProtocolItems as $index => $modelProtocolItems): ?>
                                    <div class="item panel panel-primary"><!-- widgetItem -->
                                        <div class="panel-heading">
                                            <span class="panel-title-activities">Учащийся: <?= ($index + 1) ?></span>
                                            <?php if (!$readonlyItems): ?>
                                                <div class="pull-right">
                                                    <button type="button" class="remove-item btn btn-default btn-xs">
                                                        удалить
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <?php

                                                // necessary for update action.
                                                if (!$modelProtocolItems->isNewRecord) {
                                                    echo Html::activeHiddenInput($modelProtocolItems, "[{$index}]id");
                                                }
                                                ?>
                                                <?php
                                                echo Html::activeHiddenInput($modelProtocolItems, "[{$index}]schoolplan_protocol_id");
                                                ?>

                                                <?= $form->field($modelProtocolItems, "[{$index}]studyplan_subject_id")->widget(\kartik\select2\Select2::class, [
                                                    'data' => $model->getStudyplanSubjectList(),
                                                    'showToggleAll' => false,
                                                    'options' => [
                                                        'disabled' => $readonlyItems,
                                                        'placeholder' => Yii::t('art', 'Select...'),
                                                        'multiple' => false,
                                                    ],
                                                    'pluginOptions' => [
                                                        'allowClear' => false,
                                                    ],

                                                ]);
                                                ?>

                                            </div>
                                            <div class="row markForm_<?= $index ?>">
                                                <?= $form->field($modelProtocolItems, "[{$index}]lesson_mark_id")->widget(\kartik\select2\Select2::class, [
                                                    'data' => RefBook::find('lesson_mark')->getList(),
                                                    'showToggleAll' => false,
                                                    'options' => [
                                                        'disabled' => $readonlyItems,
                                                        'placeholder' => Yii::t('art', 'Select...'),
                                                        'multiple' => false,
                                                    ],
                                                    'pluginOptions' => [
                                                        'allowClear' => false,
                                                    ],
                                                ]);
                                                ?>
                                            </div>
                                            <div class="row">


                                                <?= $form->field($modelProtocolItems, "[{$index}]resume")->textarea(['rows' => 3, 'maxlength' => true, 'readonly' => $readonlyItems]) ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <?php endforeach; ?>
                                <?php if (!$readonlyItems): ?>
                                    <div class="panel-footer">
                                        <div class="form-group btn-group">

                                            <button type="button"
                                                    class="add-item btn btn-success btn-sm pull-right">
                                                <i class="glyphicon glyphicon-plus"></i> Добавить
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div><!-- .panel -->
                        </div>
                    </div>
                </div>
            <?php DynamicFormWidget::end(); ?>
            <?php endif; ?>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= !$readonlyItems ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
