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
/* @var $modelsDependence */

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
                        'data' => RefBook::find('education_programm_name', $model->isNewRecord ? \common\models\education\EducationProgramm::STATUS_ACTIVE : '')->getList(),
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

                    <?= $form->field($model, "speciality_id")->widget(DepDrop::class, [
                        'data' => \common\models\education\EducationProgramm::getSpecialityByProgramm($model->programm_id),
                        'options' => ['prompt' => Yii::t('art/studyplan', 'Select Education Speciality...'),
                            'disabled' => $model->speciality_id ? true : $readonly,
                        ],
                        'pluginOptions' => [
                            'depends' => ['programm_id'],
                            'placeholder' => Yii::t('art/guide', 'Select Education Speciality...'),
                            'url' => Url::to(['/studyplan/default/speciality'])
                        ]
                    ]);
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

                    <?= $form->field($model, 'status')->dropDownList(Studyplan::getStatusList(), ['disabled' => $readonly]) ?>

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
                                    'model' => $modelsDependence[0],
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
                                    <thead>
                                    <tr>
                                        <th class="text-center">Раздел дисциплины</th>
                                        <th class="text-center">Дисциплина</th>
                                        <th class="text-center">Тип занятий</th>
                                        <th class="text-center">Форма занятий</th>
                                        <th class="text-center">Часов в неделю</th>
                                        <th class="text-center">Часов в год</th>
                                        <!--                    --><?php //if ($model->catType != 1000): ?>
                                        <th class="text-center">Стоимость часа</th>
                                        <th class="text-center">Оплата в месяц</th>
                                        <th class="text-center">Сумма в рублях за учебный год</th>
                                        <!--                    --><?php //else: ?>
                                        <th class="text-center">Консультации - часов в год</th>
                                        <!--                    --><?php //endif; ?>
                                        <th class="text-center">
                                            <?php if (!$readonly): ?>
                                                <button type="button" class="add-item btn btn-success btn-xs"><span
                                                            class="fa fa-plus"></span></button>
                                            <?php endif; ?>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="container-items">
                                    <?php foreach ($modelsDependence as $index => $modelDependence): ?>
                                        <tr class="item">
                                            <?php
                                            // necessary for update action.
                                            if (!$modelDependence->isNewRecord) {
                                                echo Html::activeHiddenInput($modelDependence, "[{$index}]id");
                                            }
                                            ?>
                                            <td>
                                                <?php
                                                $field = $form->field($modelDependence, "[{$index}]subject_cat_id");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \kartik\select2\Select2::widget(
                                                        [
                                                            'model' => $modelDependence,
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
                                                $field = $form->field($modelDependence, "[{$index}]subject_id");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \kartik\depdrop\DepDrop::widget(
                                                        [
                                                            'model' => $modelDependence,
                                                            'attribute' => "[{$index}]subject_id",
                                                            'data' => $model->getSubjectByCategory($modelDependence->subject_cat_id),
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
                                                $field = $form->field($modelDependence, "[{$index}]subject_type_id");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \kartik\select2\Select2::widget(
                                                        [
                                                            'model' => $modelDependence,
                                                            'attribute' => "[{$index}]subject_type_id",
                                                            'data' => \common\models\education\EducationSpeciality::getTypeList($model->speciality_id),
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
                                                $field = $form->field($modelDependence, "[{$index}]subject_vid_id");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \kartik\select2\Select2::widget(
                                                        [
                                                            'model' => $modelDependence,
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
                                                $field = $form->field($modelDependence, "[{$index}]week_time");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelDependence, "[{$index}]week_time", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelDependence, "[{$index}]year_time");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelDependence, "[{$index}]year_time", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelDependence, "[{$index}]cost_hour");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelDependence, "[{$index}]cost_hour", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelDependence, "[{$index}]cost_month_summ");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelDependence, "[{$index}]cost_month_summ", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelDependence, "[{$index}]cost_year_summ");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelDependence, "[{$index}]cost_year_summ", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $field = $form->field($modelDependence, "[{$index}]year_time_consult");
                                                echo $field->begin();
                                                ?>
                                                <div class="col-sm-12">
                                                    <?= \yii\helpers\Html::activeTextInput($modelDependence, "[{$index}]year_time_consult", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                                                    <p class="help-block help-block-error"></p>
                                                </div>
                                                <?= $field->end(); ?>
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
                                <?= Html::submitButton('<i class="fa fa-send-o" aria-hidden="true"></i> Отправить документы на электронную почту', ['class' => 'btn btn-sm btn-warning', 'name' => 'submitAction', 'value' => 'doc_send']); ?>
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
