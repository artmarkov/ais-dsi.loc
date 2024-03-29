<?php

use artsoft\widgets\ActiveForm;
use common\models\own\Department;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\EntrantComm */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="entrant-comm-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка вступительных экзаменов
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('Введите название. Например: Комиссия Муз.Отделение 2023/2024 уч.год') ?>

                    <?= $form->field($model, 'division_id')->widget(\kartik\select2\Select2::className(), [
                        'data' => \common\models\own\Division::getDivisionList(),
                        'options' => [
                            'id' => 'division_id',
                             'disabled' => $readonly,
                            'placeholder' => Yii::t('art/guide', 'Select Division...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Division'));
                    ?>
                    <?= $form->field($model, 'department_list')->widget(\kartik\depdrop\DepDrop::className(), [
                        'data' => Department::getDepartmentListByDivision($model->division_id),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                            'multiple' => true,
                        ],
                        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                        'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                         'pluginOptions' => [
                            'depends' => ['division_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => Url::to(['/entrant/default/department'])
                        ]
                    ])->label(Yii::t('art/guide', 'Department'));
                    ?>
                    <?= $form->field($model, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                            'disabled' => $readonly,
                            'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => $model->isNewRecord ? true : false]
                            ]
                        ]);
                    ?>

<!--                   = $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true]) -->

                    <?= $form->field($model, 'timestamp_in')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                    <?= $form->field($model, 'timestamp_out')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>

                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Экзаменационная комиссия
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Испытания
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'prep_on_test_list')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\helpers\RefBook::find('entrant_test_name')->getList(),
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
                            <?= $form->field($model, 'prep_off_test_list')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\helpers\RefBook::find('entrant_test_name')->getList(),
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
                </div>
            </div>
            <!--<div class="panel panel-info">
                <div class="panel-heading">
                    Связанные события из плана работы
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                        </div>
                    </div>
                </div>
            </div>-->
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
