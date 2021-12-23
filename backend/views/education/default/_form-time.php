<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $modelsEducationProgrammLevelSubject */
/* @var $model */
/* @var $index */
/* @var $readonly */

?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_inner',
    'widgetBody' => '.container-time',
    'widgetItem' => '.room-item',
    'limit' => 8,
    'min' => 1,
    'insertButton' => '.add-time',
    'deleteButton' => '.remove-time',
    'model' => $modelsEducationProgrammLevelSubject[0],
    'formId' => 'education-programm-form',
    'formFields' => [
        'subject_cat_id',
        'subject_id',
        'subject_vid_id',
        'week_time',
        'year_time',
        'cost_hour',
        'cost_month_summ',
        'cost_year_summ',
        'year_time_consult'
    ],
]); ?>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center" style="min-width: 100px">Раздел</br>учебных</br>предметов</th>
        <th class="text-center" style="min-width: 100px">Предмет</th>
        <th class="text-center" style="min-width: 100px">Форма</br>занятий</th>
        <th class="text-center">Часов</br>в неделю</th>
        <th class="text-center">Часов</br>в год</th>
        <?php if ($model->catType != \common\models\education\EducationCat::BASIS_FREE): ?>
            <th class="text-center">Стоимость часа</th>
            <th class="text-center">Оплата в месяц</th>
            <th class="text-center">Сумма в рублях за учебный год</th>
        <?php else: ?>
            <th class="text-center">Консультации</br>часов в год</th>
        <?php endif; ?>
        <th class="text-center">
            <?php if (!$readonly): ?>
                <button type="button" class="add-time btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
            <?php endif; ?>
        </th>
    </tr>
    </thead>
    <tbody class="container-time">
    <?php foreach ($modelsEducationProgrammLevelSubject as $indexTime => $modelEducationProgrammLevelSubject): ?>
        <tr class="room-item">
            <?php
            // necessary for update action.
            if (!$modelEducationProgrammLevelSubject->isNewRecord) {
                echo Html::activeHiddenInput($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]id");
            }
            ?>
            <td>
                <?php
                $field = $form->field($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]subject_cat_id");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= \kartik\select2\Select2::widget(
                        [
                            'model' => $modelEducationProgrammLevelSubject,
                            'attribute' => "[{$index}][{$indexTime}]subject_cat_id",
                            'id' => 'educationprogrammlevelsubject-' . $index . '-' . $indexTime . '-subject_cat_id',
                            'data' => \artsoft\helpers\RefBook::find('subject_category_name_dev', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList(),
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
                $field = $form->field($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]subject_id");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= \kartik\depdrop\DepDrop::widget(
                        [
                            'model' => $modelEducationProgrammLevelSubject,
                            'attribute' => "[{$index}][{$indexTime}]subject_id",
                            'id' => ['educationprogrammlevelsubject-' . $index . '-' . $indexTime . '-subject_id'],
                            'data' => $model->getSubjectByCategory($modelEducationProgrammLevelSubject->subject_cat_id),
                            'options' => [
                                'prompt' => Yii::t('art', 'Select...'),
                                'disabled' => $readonly,
                            ],
                            'pluginOptions' => [
                                'depends' => ['educationprogrammlevelsubject-' . $index . '-' . $indexTime . '-subject_cat_id'],
                                'placeholder' => Yii::t('art', 'Select...'),
                                'url' => \yii\helpers\Url::to(['/education/default/subject', 'id' => $model->id])
                            ]
                        ]
                    ) ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <?= $field->end(); ?>
            </td>
            <td>
                <?php
                $field = $form->field($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]subject_vid_id");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= \kartik\select2\Select2::widget(
                        [
                            'model' => $modelEducationProgrammLevelSubject,
                            'attribute' => "[{$index}][{$indexTime}]subject_vid_id",
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
                $field = $form->field($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]week_time");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= Html::activeTextInput($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]week_time", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <?= $field->end(); ?>
            </td>
            <td>
                <?php
                $field = $form->field($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]year_time");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= Html::activeTextInput($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]year_time", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <?= $field->end(); ?>
            </td>
            <?php if ($model->catType != \common\models\education\EducationCat::BASIS_FREE): ?>
                <td>
                    <?php
                    $field = $form->field($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]cost_hour");
                    echo $field->begin();
                    ?>
                    <div class="col-sm-12">
                        <?= Html::activeTextInput($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]cost_hour", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                        <p class="help-block help-block-error"></p>
                    </div>
                    <?= $field->end(); ?>
                </td>
                <td>
                    <?php
                    $field = $form->field($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]cost_month_summ");
                    echo $field->begin();
                    ?>
                    <div class="col-sm-12">
                        <?= Html::activeTextInput($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]cost_month_summ", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                        <p class="help-block help-block-error"></p>
                    </div>
                    <?= $field->end(); ?>
                </td>
                <td>
                    <?php
                    $field = $form->field($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]cost_year_summ");
                    echo $field->begin();
                    ?>
                    <div class="col-sm-12">
                        <?= Html::activeTextInput($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]cost_year_summ", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                        <p class="help-block help-block-error"></p>
                    </div>
                    <?= $field->end(); ?>
                </td>
            <?php else: ?>
                <td>
                    <?php
                    $field = $form->field($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]year_time_consult");
                    echo $field->begin();
                    ?>
                    <div class="col-sm-12">
                        <?= Html::activeTextInput($modelEducationProgrammLevelSubject, "[{$index}][{$indexTime}]year_time_consult", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                        <p class="help-block help-block-error"></p>
                    </div>
                    <?= $field->end(); ?>
                </td>
            <?php endif; ?>
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
