<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\RefBook;
use artsoft\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

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
?>

    <div class="studyplan-thematic-form">

<?php
$form = ActiveForm::begin([
    'id' => 'studyplan-thematic-form',
    'validateOnBlur' => false,
])
?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Тематический(репертуарный) план:
                    <?php echo RefBook::find('subject_memo_2')->getValue($model->studyplan_subject_id); ?>
                    <?php echo RefBook::find('sect_name_2')->getValue($model->subject_sect_studyplan_id); ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            echo Html::activeHiddenInput($model, 'subject_sect_studyplan_id');
                            echo Html::activeHiddenInput($model, 'studyplan_subject_id');
                            ?>

                            <?= $form->field($model, 'thematic_category')->dropDownList(\common\models\studyplan\StudyplanThematic::getCategoryList()) ?>

                            <?= $form->field($model, 'half_year')->dropDownList(\artsoft\helpers\ArtHelper::getHalfYearList());?>

                            <?= $form->field($model->loadDefaultValues(), 'doc_status')->dropDownList(\common\models\studyplan\StudyplanThematic::getDocStatusList()/*, ['disabled' => $readonly]*/) ?>

                            <?= $form->field($model, 'doc_sign_teachers_id')->widget(\kartik\select2\Select2::class, [
                                'data' => RefBook::find('teachers_fio')->getList(),
                                'options' => [
                                    'disabled' => true,
                                    'placeholder' => Yii::t('art', 'Select...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>

                            <?= $form->field($model, 'template_flag')->checkbox() ?>

                            <?= $form->field($model, 'template_name')->textInput(['maxlength' => true])->hint('Используйте уникальное название. Пример: Сольфеджио 5 класс(ФИО преподавателя)') ?>

                        </div>
                    </div>
                    <?php if (!$model->isNewRecord) : ?>

                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item', // required: css class
                        'limit' => 50, // the maximum times, an element can be added (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $modelsItems[0],
                        'formId' => 'studyplan-thematic-form',
                        'formFields' => [
                            'name',
                            'author',
                            'piece_name',
                            'piece_category',
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
                                    <?php if ($model->thematic_category == \common\models\studyplan\StudyplanThematic::REPERTORY_PLAN): ?>
                                        <th class="text-center">Автор произведения</th>
                                        <th class="text-center">Название произведения</th>
                                        <th class="text-center">Категория произведения</th>
                                    <?php endif; ?>
                                    <th class="text-center">Задание</th>
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
                                        <?php if ($model->thematic_category == \common\models\studyplan\StudyplanThematic::REPERTORY_PLAN): ?>
                                            <td>
                                                <?php
                                                $field = $form->field($modelItems, "[{$index}]author");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelItems, "[{$index}]author", ['class' => 'form-control']); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelItems, "[{$index}]piece_name");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelItems, "[{$index}]piece_name", ['class' => 'form-control']); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelItems, "[{$index}]piece_category_id");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \kartik\select2\Select2::widget(
                                                        [
                                                            'model' => $modelItems,
                                                            'attribute' => "[{$index}]piece_category_id",
                                                            'data' => RefBook::find('piece_category', \common\models\education\PieceCategory::STATUS_ACTIVE)->getList(),
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
                                        <?php endif; ?>
                                        <td>
                                            <?php
                                            $field = $form->field($modelItems, "[{$index}]task");
                                            echo $field->begin();
                                            ?>
                                            <div class="col-sm-12">
                                                <?= \yii\helpers\Html::activeTextInput($modelItems, "[{$index}]task", ['class' => 'form-control']); ?>
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


                            <?php endif; ?>

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
<?php
$js = <<<JS
    $('input[id="studyplanthematic-template_flag"]').prop('checked') ? $('.field-studyplanthematic-template_name').show() : $('.field-studyplanthematic-template_name').hide();
    $('input[name="StudyplanThematic[template_flag]"]').click(function(){
       $(this).prop('checked') ? $('.field-studyplanthematic-template_name').show() : $('.field-studyplanthematic-template_name').hide();
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>