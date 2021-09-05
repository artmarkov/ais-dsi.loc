<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $modelsTime */
/* @var $index */
/* @var $count */
/* @var $readonly */

?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_inner',
    'widgetBody' => '.container-time',
    'widgetItem' => '.room-item',
    'limit' => $count,
    'min' => 1,
    'insertButton' => '.add-time',
    'deleteButton' => '.remove-time',
    'model' => $modelsTime[0],
    'formId' => 'education-programm-form',
    'formFields' => [
        'subject_cat_id',
        'subject_id',
        'week_time',
        'year_time',
        'cost_week_hour'
    ],
]); ?>
<table class="table">
    <thead>
    <tr>
        <th class="text-center">Категория</th>
        <th class="text-center">Дисциплина</th>
        <th class="text-center">Часов в неделю</th>
        <th class="text-center">Стоимость часа</th>
        <th class="text-center">Консультации</br>часов в год</th>
        <th class="text-center">
            <?php if (!$readonly): ?>
                <button type="button" class="add-time btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
            <?php endif; ?>
        </th>
    </tr>
    </thead>
    <tbody class="container-time">
    <?php foreach ($modelsTime as $indexTime => $modelTime): ?>
        <tr class="room-item">
            <?php
            // necessary for update action.
            if (!$modelTime->isNewRecord) {
                echo Html::activeHiddenInput($modelTime, "[{$index}][{$indexTime}]id");
            }
            ?>
<!--            --><?//= $form->field($modelTime, "[{$index}]subject_cat_id")->widget(\kartik\select2\Select2::class, [
//                'data' => RefBook::find('subject_category_name', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList(),
//                'options' => [
//                    'disabled' => $readonly,
//                    'placeholder' => Yii::t('art/guide', 'Select Subject Category...'),
//                ],
//                'pluginOptions' => [
//                    'allowClear' => true
//                ],
//            ]);
//            ?>
<!---->
<!--            --><?//= $form->field($modelTime, "[{$index}]subject_id")->widget(DepDrop::class, [
//                'data' => $model->getSubjectByCategory($modelSubject->subject_cat_id),
//                'options' => ['prompt' => Yii::t('art/guide', 'Select Subject Name...'),
//                    'disabled' => $readonly,
//                ],
//                'pluginOptions' => [
//                    'depends' => ['educationprogrammsubject-' . $index . '-subject_cat_id'],
//                    'placeholder' => Yii::t('art/guide', 'Select Subject Name...'),
//                    'url' => Url::to(['/education/default/subject', 'id' => $model->id])
//                ]
//            ]);
//            ?>
            <td>
                <?= $form->field($modelTime, "[{$index}][{$indexTime}]subject_cat_id",['template' => '{input}{error}{label}'])->label(false)->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?= $form->field($modelTime, "[{$index}][{$indexTime}]subject_id")->label(false)->textInput(['maxlength' => true]) ?>
            </td>

            <td>
                <?= $form->field($modelTime, "[{$index}][{$indexTime}]week_time")->label(false)->textInput(['maxlength' => true]) ?>
            </td>
             <td>
                <?= $form->field($modelTime, "[{$index}][{$indexTime}]cost_week_hour")->label(false)->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?= $form->field($modelTime, "[{$index}][{$indexTime}]year_time")->label(false)->textInput(['maxlength' => true]) ?>
            </td>
            <td class="vcenter">
                <?php if (!$readonly): ?>
                    <button type="button" class="remove-time btn btn-danger btn-xs"><span
                                class="fa fa-minus"></span></button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php DynamicFormWidget::end(); ?>
