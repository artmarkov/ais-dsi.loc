<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;

/* @var $modelsTeachersLoad */
/* @var $index */
/* @var $readonly */

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
    , \yii\web\View::POS_END);
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
<table class="table table-responsive">
    <thead>
    <tr>
        <th class="text-center" style="min-width: 100px">Деятельность</th>
        <th class="text-center" style="min-width: 300px">Преподаватель</th>
        <th class="text-center">Нагрузка</th>
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
                            'data' => \common\models\guidejob\Direction::getDirectionList(),
                            'options' => [
                                'id' => 'teachersLoad-' . $index . '-' . $indexLoad . '-direction_id',
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
                            'data' => \common\models\teachers\Teachers::getTeachersList($modelTeachersLoad->direction_id),
                            'options' => [
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                            ],
                            'pluginOptions' => [
                                'depends' => ['teachersLoad-' . $index . '-' . $indexLoad . '-direction_id'],
                                'placeholder' => Yii::t('art', 'Select...'),
                                'url' => Url::to(['/teachers/default/teachers'])
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
