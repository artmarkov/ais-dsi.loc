<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\education\EntrantPreregistrations;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EntrantPreregistrations */
/* @var $form artsoft\widgets\ActiveForm */

$readonly = $model->status == EntrantPreregistrations::REG_STATUS_STUDENT;
?>

<div class="entrant-preregistrations-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'entrant-preregistrations-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка предварительной записи
            <div class="pull-right">
            <?php
            if ($model->student_id) {
                echo Html::a('<i class="fa fa-user-o" aria-hidden="true"></i> Открыть в новом окне',
                    ['students/default/view', 'id' => $model->student_id],
                    [
                        'target' => '_blank',
                        'class' => 'btn btn-default',
                    ]);
            }
            ?>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'student_id')->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('students_fullname')->getList(),
                        'options' => [
                            'disabled' => $model->student_id ? true : false,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'entrant_programm_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\education\EntrantProgramm::getEntrantProgrammList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->hint('Необходимо выбрать программу для предварительной записи.');
                    ?>

                    <?= $form->field($model, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                             'disabled' => $readonly,
                            'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => $model->isNewRecord ? true : false]
                            ]
                        ]);
                    ?>
                    <?= $form->field($model, 'reg_vid')->radioList(EntrantPreregistrations::getRegList()/*, ['itemOptions' => ['disabled' => $readonly]]*/) ?>

                    <?= $form->field($model, 'status')->dropDownList(EntrantPreregistrations::getRegStatusList(), ['disabled' => $readonly])->hint('При смене статуса "Принят на обучение", будет создан учебный план автоматически.') ?>
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
