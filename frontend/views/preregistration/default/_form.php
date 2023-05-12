<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\education\EntrantPreregistrations;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EntrantPreregistrations */
/* @var $form artsoft\widgets\ActiveForm */
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
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    echo Html::activeHiddenInput($model, 'student_id');
                    echo Html::activeHiddenInput($model, 'plan_year');
                    echo Html::activeHiddenInput($model, 'reg_vid');
                    echo Html::activeHiddenInput($model, 'status');
                    ?>

                    <?= $form->field($model, 'entrant_programm_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\education\EntrantProgramm::getEntrantProgrammLimitList($age, $plan_year),
                        'options' => [
//                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->hint('Необходимо выбрать программу для предварительной записи.');
                    ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::saveButton() ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
