<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $modelsTeachersLoad */
/* @var $model */
/* @var $index */
/* @var $readonly */

?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_inner',
    'widgetBody' => '.container-time',
    'widgetItem' => '.room-item',
    'limit' => 4,
    'min' => 1,
    'insertButton' => '.add-time',
    'deleteButton' => '.remove-time',
    'model' => $modelsTeachersLoad[0],
    'formId' => 'subject-sect-form',
    'formFields' => [
        'direction_id',
        'teachers_id',
        'week_time'
    ],
]); ?>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center" style="min-width: 100px">Вид</br>деятельности</th>
        <th class="text-center" style="min-width: 100px">Преподаватель</th>
        <th class="text-center">Часов</br>в неделю</th>
        <th class="text-center">
            <?php if (!$readonly): ?>
                <button type="button" class="add-time btn btn-success btn-xs"><span class="fa fa-plus"></span></button>
            <?php endif; ?>
        </th>
    </tr>
    </thead>
    <tbody class="container-time">
    <?php foreach ($modelsTeachersLoad as $indexLoad => $modelTeachersLoad): ?>
        <tr class="room-item">
            <?php
            // necessary for update action.
            if (!$modelTeachersLoad->isNewRecord) {
                echo Html::activeHiddenInput($modelTeachersLoad, "[{$index}][{$indexLoad}]id");
            }
            ?>
            <td>
                <?php
                $field = $form->field($modelTeachersLoad, "[{$index}][{$indexLoad}]direction_id");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= \kartik\select2\Select2::widget(
                        [
                            'model' => $modelTeachersLoad,
                            'attribute' => "[{$index}][{$indexLoad}]direction_id",
                            'id' => 'TeachersLoad-' . $index . '-' . $indexLoad . '-direction_id',
//                            'data' => \artsoft\helpers\RefBook::find('subject_category_name_dev', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList(),
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
                $field = $form->field($modelTeachersLoad, "[{$index}][{$indexLoad}]teachers_id");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= \kartik\depdrop\DepDrop::widget(
                        [
                            'model' => $modelTeachersLoad,
                            'attribute' => "[{$index}][{$indexLoad}]teachers_id",
                            'id' => ['TeachersLoad-' . $index . '-' . $indexLoad . '-direction_id'],
//                            'data' => $model->getSubjectByCategory($modelTeachersLoad->subject_cat_id),
                            'options' => [
                                'prompt' => Yii::t('art', 'Select...'),
                                'disabled' => $readonly,
                            ],
                            'pluginOptions' => [
                                'depends' => ['TeachersLoad-' . $index . '-' . $indexLoad . '-direction_id'],
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
                $field = $form->field($modelTeachersLoad, "[{$index}][{$indexLoad}]week_time");
                echo $field->begin();
                ?>
                <div class="col-sm-12">
                    <?= Html::activeTextInput($modelTeachersLoad, "[{$index}][{$indexLoad}]week_time", ['class' => 'form-control', 'disabled' => $readonly]); ?>
                    <p class="help-block help-block-error"></p>
                </div>
                <?= $field->end(); ?>
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
