<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\education\EntrantProgramm;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\EntrantProgramm */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="entrant-programm-form">

    <?php 
    $form = ActiveForm::begin([
            'id' => 'entrant-programm-form',
            'validateOnBlur' => false,
        ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка программы для предварительной записи
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, "programm_id")->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('education_programm_short_name', $model->isNewRecord ? \common\models\education\EducationProgramm::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'id' => 'programm_id',
                            'disabled' => false,
                            'placeholder' => Yii::t('art/studyplan', 'Select Education Programm...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])->label(Yii::t('art/studyplan', 'Education Programm'));
                    ?>

                    <?= $form->field($model, 'subject_type_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\subject\SubjectType::getTypeList(),
                        'options' => [
                            'disabled' =>  false,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Subject Type'));
                    ?>

                    <?= $form->field($model, 'course')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                        'options' => [
                            'disabled' => false,
                            'placeholder' => Yii::t('art/guide', 'Select Course...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Course'));
                    ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->hint('Это название увидят пользователи при выборе программы'); ?>

                    <?= $form->field($model, 'age_in')->widget(kartik\touchspin\TouchSpin::class)->hint('Укажите минимальный возраст ученика для выьранной программы'); ?>

                    <?= $form->field($model, 'age_out')->widget(kartik\touchspin\TouchSpin::class)->hint('Укажите максимальный возраст ученика для выьранной программы'); ?>

                    <?= $form->field($model, 'qty_entrant')->widget(kartik\touchspin\TouchSpin::class)->hint('Укажите колличество детей для приема'); ?>

                    <?= $form->field($model, 'qty_reserve')->widget(kartik\touchspin\TouchSpin::class)->hint('Укажите колличество детей для приема в резерв'); ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('Это описание увидят родители при выборе программы') ?>

                    <?= $form->field($model, 'status')->dropDownList(EntrantProgramm::getStatusList()) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?=  \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?=  \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php  ActiveForm::end(); ?>

</div>
