<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\studyplan\Studyplan;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\Studyplan */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */
/* @var $modelsStudyplanSubject */

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
    , \yii\web\View::POS_END);

?>

<div class="studyplan-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'studyplan-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Основные данные
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, "student_id")->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('students_fullname', $model->isNewRecord ? \common\models\user\UserCommon::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'disabled' => $model->student_id ? true : $readonly,
                            'placeholder' => Yii::t('art/studyplan', 'Select Student...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])->label(Yii::t('art/student', 'Student'));
                    ?>
                    <?= $form->field($model, "programm_id")->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('education_programm_short_name', $model->isNewRecord ? \common\models\education\EducationProgramm::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'id' => 'programm_id',
                            'disabled' => $model->programm_id ? true : $readonly,
                            'placeholder' => Yii::t('art/studyplan', 'Select Education Programm...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])->label(Yii::t('art/studyplan', 'Education Programm'));
                    ?>

                    <?= $form->field($model, 'subject_form_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\subject\SubjectForm::getFormList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Subject Form'));
                    ?>

                    <?= $form->field($model, 'course')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                        'options' => [
                            'disabled' => $model->course ? true : $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Course...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Course'));
                    ?>

                    <?= $form->field($model, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                            'disabled' => $model->plan_year ? true : $readonly,
                            'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => $model->isNewRecord ? true : false]
                            ]
                        ]);
                    ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                </div>
            </div>
            <?php if (!$model->isNewRecord) : ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Учебная нагрузка
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php DynamicFormWidget::begin([
                                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                                    'widgetBody' => '.container-items', // required: css class selector
                                    'widgetItem' => '.item', // required: css class
                                    'limit' => 999, // the maximum times, an element can be added (default 999)
                                    'min' => 1, // 0 or 1 (default 1)
                                    'insertButton' => '.add-item', // css class
                                    'deleteButton' => '.remove-item', // css class
                                    'model' => $modelsStudyplanSubject[0],
                                    'formId' => 'studyplan-form',
                                    'formFields' => [
                                        'subject_cat_id',
                                        'subject_id',
                                        'subject_type_id',
                                        'week_time',
                                        'year_time',
                                        'cost_hour',
                                        'cost_month_summ',
                                        'cost_year_summ',
                                        'year_time_consult',
                                    ],
                                ]); ?>
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-warning">
                                    <tr>
                                        <th class="text-center" style="min-width: 100px">Раздел</br>учебных</br>
                                            предметов
                                        </th>
                                        <th class="text-center" style="min-width: 150px">Предмет</th>
                                        <th class="text-center" style="min-width: 150px">Тип</br>занятий</th>
                                        <th class="text-center" style="min-width: 150px">Вид</br>занятий</th>
                                        <th class="text-center">Часов</br>в неделю</th>
                                        <th class="text-center">Часов</br>в год</th>
                                        <!--                    --><?php //if ($model->catType != 1000): ?>
                                        <th class="text-center">Стоимость часа</th>
                                        <th class="text-center">Оплата в месяц</th>
                                        <th class="text-center">Сумма в рублях за учебный год</th>
                                        <!--                    --><?php //else: ?>
                                        <th class="text-center">Консультации</br>часов в год</th>
                                        <!--                    --><?php //endif; ?>
                                        <th class="text-center">Промежуточная</br>аттестация</th>
                                        <th class="text-center">Итоговая</br>аттестация</th>
                                        <th class="text-center">
                                            <?php if (!$readonly): ?>
                                                <button type="button" class="add-item btn btn-success btn-xs"><span
                                                            class="fa fa-plus"></span></button>
                                            <?php endif; ?>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="container-items">
                                    <?php
                                    $sum_week_time = 0;
                                    $sum_year_time = 0;
                                    $sum_year_time_consult = 0;
                                    ?>
                                    <?php foreach ($modelsStudyplanSubject as $index => $modelStudyplanSubject): ?>
                                        <?php
                                        $sum_week_time += $modelStudyplanSubject->week_time;
                                        $sum_year_time += $modelStudyplanSubject->year_time;
                                        $sum_year_time_consult += $modelStudyplanSubject->year_time_consult;
                                        ?>
                                        <tr class="item">
                                            <?php
                                            // necessary for update action.
                                            if (!$modelStudyplanSubject->isNewRecord) {
                                                echo Html::activeHiddenInput($modelStudyplanSubject, "[{$index}]id");
                                            }
                                            ?>
                                            <td>
                                                <?php
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]subject_cat_id");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \kartik\select2\Select2::widget(
                                                        [
                                                            'model' => $modelStudyplanSubject,
                                                            'attribute' => "[{$index}]subject_cat_id",
                                                            'data' => \artsoft\helpers\RefBook::find('subject_category_name_dev', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList(),
                                                            'options' => [
                                                                'id' => 'studyplansubject-' . $index . '-subject_cat_id',

                                                                'disabled' => $readonly,
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
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]subject_id");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \kartik\depdrop\DepDrop::widget(
                                                        [
                                                            'model' => $modelStudyplanSubject,
                                                            'attribute' => "[{$index}]subject_id",
                                                            'data' => $model->getSubjectByCategory($modelStudyplanSubject->subject_cat_id),
                                                            'options' => [
                                                                'prompt' => Yii::t('art', 'Select...'),
                                                                'disabled' => $readonly,
                                                            ],
                                                            'pluginOptions' => [
                                                                'depends' => ['studyplansubject-' . $index . '-subject_cat_id'],
                                                                'placeholder' => Yii::t('art', 'Select...'),
                                                                'url' => \yii\helpers\Url::to(['/studyplan/default/subject', 'id' => $model->id])
                                                            ]
                                                        ]
                                                    ) ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]subject_type_id");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \kartik\select2\Select2::widget(
                                                        [
                                                            'model' => $modelStudyplanSubject,
                                                            'attribute' => "[{$index}]subject_type_id",
                                                            'data' => \common\models\subject\SubjectType::getTypeList(),
                                                            'options' => [

                                                                'disabled' => $readonly,
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
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]subject_vid_id");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \kartik\select2\Select2::widget(
                                                        [
                                                            'model' => $modelStudyplanSubject,
                                                            'attribute' => "[{$index}]subject_vid_id",
                                                            'data' => \artsoft\helpers\RefBook::find('subject_vid_name_dev', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList(),
                                                            'options' => [

                                                                'disabled' => $readonly,
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
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]week_time");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelStudyplanSubject, "[{$index}]week_time", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]year_time");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelStudyplanSubject, "[{$index}]year_time", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]cost_hour");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelStudyplanSubject, "[{$index}]cost_hour", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]cost_month_summ");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelStudyplanSubject, "[{$index}]cost_month_summ", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]cost_year_summ");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelStudyplanSubject, "[{$index}]cost_year_summ", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelStudyplanSubject, "[{$index}]year_time_consult");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelStudyplanSubject, "[{$index}]year_time_consult", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?= $form->field($modelStudyplanSubject, "[{$index}]med_cert")->checkbox(['disabled' => $readonly, 'label' => 'Да']) ?>
                                            </td>
                                            <td>
                                                <?= $form->field($modelStudyplanSubject, "[{$index}]fin_cert")->checkbox(['disabled' => $readonly, 'label' => 'Да']) ?>
                                            </td>
                                            <td class="vcenter">
                                                <?php if (!$readonly): ?>
                                                    <button type="button"
                                                            class="remove-item btn btn-danger btn-xs"><span
                                                                class="fa fa-minus"></span></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-warning">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= $sum_week_time; ?></td>
                                        <td><?= $sum_year_time; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= $sum_year_time_consult; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                                <?php DynamicFormWidget::end(); ?>
                            </div>
                        </div>
                        <div class="row">

                            <?= $form->field($model, "[{$index}]year_time_total")->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>

                            <?= $form->field($model, "[{$index}]cost_month_total")->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>

                            <?= $form->field($model, "[{$index}]cost_year_total")->textInput(['maxlength' => true, 'disabled' => $readonly]) ?>
                        </div>
                    </div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Статус учебного плана
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <?= $form->field($model, 'status')->dropDownList(Studyplan::getStatusList(), ['disabled' => true]) ?>

                            <?= $form->field($model, 'status_reason')->dropDownList(Studyplan::getStatusReasonList(), ['disabled' => true]) ?>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <?php if (!$model->isNewRecord): ?>
                            <?php echo \yii\bootstrap\Alert::widget([
                                'body' => '<i class="fa fa-info-circle"></i> При отсутствии учебной программы при переводе, будет закрыт текущий учебный план без формирования нового.',
                                'options' => ['class' => 'alert-info'],
                            ]);
                            ?>
                        <?php endif; ?>
                        <div class="form-group btn-group">
                            <?php if (!$model->isNewRecord): ?>
                                <?= Html::submitButton('<i class="fa fa-arrow-up" aria-hidden="true"></i> Перевести в следующий класс', ['class' => 'btn btn-sm btn-primary', 'name' => 'submitAction', 'value' => 'next_class', 'disabled' => $model->status == 0]); ?>
                                <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Повторить учебную программу', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'repeat_class', 'disabled' => $model->status == 0]); ?>
                                <?= Html::submitButton('<i class="fa fa-arrow-down" aria-hidden="true"></i> Завершить учебную программу', ['class' => 'btn btn-sm btn-default', 'name' => 'submitAction', 'value' => 'finish_plan', 'disabled' => $model->status == 0]); ?>
                                <?= Html::submitButton('<i class="fa fa-arrow-left" aria-hidden="true"></i> Отменить решение', ['class' => 'btn btn-sm btn-danger', 'name' => 'submitAction', 'value' => 'restore', 'disabled' => $model->status == 1]); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Документы
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'doc_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => $readonly]); ?>

                                <?= $form->field($model, 'doc_contract_start')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => $readonly]); ?>

                                <?= $form->field($model, 'doc_contract_end')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => $readonly]); ?>

                                <?= $form->field($model, "doc_signer")->widget(\kartik\select2\Select2::class, [
                                    'data' => RefBook::find('parents_dependence_fio', $model->student_id)->getList(),
                                    'options' => [
                                        'disabled' => $readonly,
                                        'placeholder' => Yii::t('art/parents', 'Select Parents...'),
                                        'multiple' => false,
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ]
                                ]);
                                ?>

                                <?= $form->field($model, 'doc_received_flag')->checkbox(['disabled' => $readonly]) ?>

                                <?= $form->field($model, 'doc_sent_flag')->checkbox(['disabled' => $readonly]) ?>

                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group btn-group">
                            <?php if (!$model->isNewRecord): ?>
                                <?= Html::submitButton('<i class="fa fa-file-word-o" aria-hidden="true"></i> Скачать договор', ['class' => 'btn btn-sm btn-primary', 'name' => 'submitAction', 'value' => 'doc_contract']); ?>
                                <?= Html::submitButton('<i class="fa fa-file-word-o" aria-hidden="true"></i> Скачать заявление', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'doc_statement']); ?>
                                <?php /*Html::submitButton('<i class="fa fa-send-o" aria-hidden="true"></i> Отправить документы на электронную почту', ['class' => 'btn btn-sm btn-warning', 'name' => 'submitAction', 'value' => 'doc_send']);*/ ?>
                            <?php endif; ?>
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
<?php
$js = <<<JS
$("select[name='Studyplan[status]']").find(":selected").val() === '0' ? $('.field-studyplan-status_reason').show() : $('.field-studyplan-status_reason').hide();
document.getElementById("studyplan-status").onchange = function () {
 $(this).val() === '0' ? $('.field-studyplan-status_reason').show() : $('.field-studyplan-status_reason').hide();
}
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>
