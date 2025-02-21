<?php

use artsoft\widgets\ActiveForm;
use common\models\question\QuestionAttribute;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\concourse\ConcourseAnswers */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $modelsItems */
/* @var $objectId */

$criteriaList = $model->attributesCriteria();
$mark_list = $model->getMarkList();
$modelItem = \common\models\concourse\ConcourseItem::findOne($objectId);
?>

<div class="concourse-answers-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => false]
        ],
        'id' => 'concourse-answers-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?php if (\artsoft\Art::isBackend()): ?>
                <div>Карточка оценки конкурсной работы: <?= $modelItem->name; ?></div>
                <div>Пользователь: <?= $model->getUserFio(); ?></div>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="text-center">№</th>
                    <th class="text-center">Критерий оценки</th>
                    <th class="text-center">Оценка</th>
                    <th class="text-center">Примечание</th>
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
                            <?= Html::activeHiddenInput($modelItems, "[{$index}]concourse_criteria_id"); ?>
                            <?= $criteriaList[$modelItems->concourse_criteria_id]; ?>
                        </td>
                        <td>
                            <?php
                            $field = $form->field($modelItems, "[{$index}]concourse_mark");
                            echo $field->begin();
                            ?>
                            <div class="col-sm-12">
                                <?= \kartik\select2\Select2::widget(
                                    [
                                        'model' => $modelItems,
                                        'attribute' => "[{$index}]concourse_mark",
                                        'data' => $mark_list,
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
                            $field = $form->field($modelItems, "[{$index}]concourse_string");
                            echo $field->begin();
                            ?>
                            <div class="col-sm-12">
                                <?= \yii\helpers\Html::activeTextInput($modelItems, "[{$index}]concourse_string", ['class' => 'form-control']); ?>
                                <p class="help-block help-block-error"></p>
                            </div>
                            <?= $field->end(); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer">
        <div class="form-group btn-group">
            <?= \artsoft\helpers\ButtonHelper::exitButton(['/concourse/default/concourse-item', 'id' => $id, 'objectId' => $objectId, 'mode' => 'update']); ?>
            <?= \artsoft\helpers\ButtonHelper::saveButton('submitAction', 'saveexit', 'Save & Exit', 'btn-md'); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
