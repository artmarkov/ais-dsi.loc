<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\RefBook;
use artsoft\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use common\models\teachers\Teachers;
use artsoft\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\StudyplanThematic */
/* @var $form artsoft\widgets\ActiveForm */

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

$readonly = in_array($model->doc_status, [1,2]) && \artsoft\Art::isFrontend() ? true : $readonly;
?>

    <div class="studyplan-thematic-form">

        <?php
        $form = ActiveForm::begin([
            'id' => 'studyplan-thematic-form',
            'validateOnBlur' => false,
        ])
        ?>

        <div class="panel">
            <div class="panel-heading">
                Тематический/репертуарный план:
                <?php echo RefBook::find('subject_memo_4')->getValue($model->studyplan_subject_id); ?>
                <?php echo RefBook::find('sect_name_1')->getValue($model->subject_sect_studyplan_id); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                        echo Html::activeHiddenInput($model, 'subject_sect_studyplan_id');
                        echo Html::activeHiddenInput($model, 'studyplan_subject_id');
                        ?>

                        <?= $form->field($model, 'half_year')->dropDownList(\artsoft\helpers\ArtHelper::getHalfYearList(true), ['disabled' => $readonly]); ?>

                        <?php
                        if ($model->isNewRecord and \artsoft\Art::isFrontend()) {

                            echo $form->field($model, 'thematic_flag')->checkbox(['disabled' => $readonly])->label('Взять из шаблона');

                            echo $form->field($model, 'thematic_list')->widget(\kartik\select2\Select2::class, [
                                'data' => $model->getTemplateList(),
                                'options' => [
                                    'disabled' => $readonly,
                                    'placeholder' => Yii::t('art', 'Select...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label('Список шаблонов');
                        } else {

                            echo $form->field($model, 'template_flag')->checkbox(['disabled' => $readonly]);

                            echo $form->field($model, 'template_name')->textInput(['maxlength' => true])->hint('Используйте уникальное название. Пример: Сольфеджио 5/8 ПП');

                        }
                        ?>

                    </div>
                </div>

                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 50, // the maximum times, an element can be added (default 999)
                    'min' => $model->isNewRecord ? 0 : 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsItems[0],
                    'formId' => 'studyplan-thematic-form',
                    'formFields' => [
                        'topic',
                        'task',
                    ],
                ]); ?>

                <div class="panel panel-info">
                    <div class="panel-heading">
                        Злементы плана
                    </div>
                    <div class="panel-body">

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th class="text-center">№</th>
                                <th class="text-center">Тема урока/Репертуар</th>
                                <th class="text-center">Примечания</th>
                                <th class="text-center">
                                    <?php if (!$readonly): ?>
                                        <button type="button" class="add-item btn btn-success btn-xs"><span
                                                    class="fa fa-plus"></span></button>
                                    <?php endif; ?>
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
                                        $field = $form->field($modelItems, "[{$index}]topic");
                                        echo $field->begin();
                                        ?>
                                        <div class="col-sm-12">
                                            <?= \yii\helpers\Html::activeTextInput($modelItems, "[{$index}]topic", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                            <p class="help-block help-block-error"></p>
                                        </div>
                                        <?= $field->end(); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $field = $form->field($modelItems, "[{$index}]task");
                                        echo $field->begin();
                                        ?>
                                        <div class="col-sm-12">
                                            <?= \yii\helpers\Html::activeTextInput($modelItems, "[{$index}]task", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                            <p class="help-block help-block-error"></p>
                                        </div>
                                        <?= $field->end(); ?>
                                    </td>

                                    <td class="vcenter text-center">
                                        <?php if (!$readonly): ?>
                                            <button type="button"
                                                    class="remove-item btn btn-danger btn-xs"><span
                                                        class="fa fa-minus"></span></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php DynamicFormWidget::end(); ?>
                    </div>
                </div>
                <?= $form->field($model->loadDefaultValues(), 'doc_status')->widget(\kartik\select2\Select2::class, [
                    'data' => \common\models\studyplan\StudyplanThematic::getDocStatusList(),
                    'showToggleAll' => false,
                    'options' => [
                        'disabled' => true,
                        'placeholder' => Yii::t('art', 'Select...'),
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => false,
                    ],
                ]);
                ?>
                <?= $form->field($model, 'doc_sign_teachers_id')->widget(\kartik\select2\Select2::class, [
                    'data' => Teachers::getTeachersByIds(User::getUsersByRole('department,administrator')),
                    'options' => [
                        'disabled' => $readonly,
                        'placeholder' => Yii::t('art', 'Select...'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->hint('Время согласования:' . Yii::$app->formatter->asDatetime($model->doc_sign_timestamp)); ?>
                <?php if (\artsoft\Art::isBackend() || (\artsoft\Art::isFrontend() && Teachers::isOwnTeacher($model->doc_sign_teachers_id))): ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'admin_flag')->checkbox()->label('Добавить сообщение преподавателю') ?>
                            <div id="admin_message">
                                <?= $form->field($model, 'sign_message')->textInput(['disabled' => false])->hint('Введите сообщение для автора') ?>
                            </div>
                            <div class="form-group btn-group pull-right">
                                <?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Согласовать', ['class' => 'btn btn-sm btn-success', 'name' => 'submitAction', 'value' => 'approve', 'disabled' => $model->doc_status == 1]); ?>
                                <?= Html::submitButton('<i class="fa fa-send-o" aria-hidden="true"></i> Отправить на доработку', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'modif', 'disabled' => $model->doc_status == 3]); ?>
                            </div>
                        </div>
                    </div>
                <?php elseif(User::hasRole(['teacher', 'department'])): ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group btn-group pull-right">
                                <?= Html::submitButton('<i class="fa fa-arrow-up" aria-hidden="true"></i> Отправить на согласование', ['class' => 'btn btn-sm btn-primary', 'name' => 'submitAction', 'value' => 'send_approve', 'disabled' => !in_array($model->doc_status, [0,3]) ? true : false]); ?>
                                <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Внести изменения', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'make_changes', 'disabled' => in_array($model->doc_status, [0,3]) ? true : false]); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : (\artsoft\Art::isBackend() ? \artsoft\helpers\ButtonHelper::viewButtons($model) : \artsoft\helpers\ButtonHelper::exitButton()); ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php
$js = <<<JS
    $('input[id="studyplanthematic-template_flag"]').prop('checked') ? $('.field-studyplanthematic-template_name').show() : $('.field-studyplanthematic-template_name').hide();
    $('input[name="StudyplanThematic[template_flag]"]').click(function(){
       $(this).prop('checked') ? $('.field-studyplanthematic-template_name').show() : $('.field-studyplanthematic-template_name').hide();
     });
     $('input[id="studyplanthematic-thematic_flag"]').prop('checked') ? $('.field-studyplanthematic-thematic_list').show() : $('.field-studyplanthematic-thematic_list').hide();
    $('input[name="StudyplanThematic[thematic_flag]"]').click(function(){
       $(this).prop('checked') ? $('.field-studyplanthematic-thematic_list').show() : $('.field-studyplanthematic-thematic_list').hide();
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);


$js = <<<JS
     // Показ модуля сообщения
    $('input[type=checkbox][name="StudyplanThematic[admin_flag]"]').prop('checked') ? $('#admin_message').show() : $('#admin_message').hide();
    $('input[type=checkbox][name="StudyplanThematic[admin_flag]"]').click(function() {
       $(this).prop('checked') ? $('#admin_message').show() : $('#admin_message').hide();
     });
  
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);
?>