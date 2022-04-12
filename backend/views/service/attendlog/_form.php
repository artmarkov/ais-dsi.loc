<?php

use artsoft\widgets\ActiveForm;
use common\models\service\UsersAttendlog;
use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\service\UsersAttendlog */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */
/* @var $modelsDependency */

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
    , \yii\web\View::POS_END);

$this->registerJs(<<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    console.log("after insert");
    var date = new Date();
    var start = ('0' + date.getDate()).slice(-2)+ '.' + ('0'+ (date.getMonth()+1)).slice(-2)+'.'+  date.getFullYear()+' '+ ('0' + date.getHours()).slice(-2)+':'+ ('0' + date.getMinutes()).slice(-2);
    
    var set = jQuery(".dynamicform_wrapper .js-slab-name");
    var length = set.length;
    set.each(function(index) {
        if(index == (length - 1)) {
                 //console.log(start);
        jQuery(this).val(start);
        }
    }); 
});
JS
    , \yii\web\View::POS_END);
?>

<div class="users-attendlog-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'users-attendlog-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= \common\models\user\UserCommon::getUserCategoryValue($model->userCommon->user_category) . ':'; ?>
            <?= $model->userCommon->getFullName() ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= Html::activeHiddenInput($model, 'user_common_id'); ?>
                </div>
            </div>

            <div class="panel panel-info">
                <div class="panel-heading">
                    Ключи от аудиторий
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php DynamicFormWidget::begin([
                                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                                'widgetBody' => '.container-items', // required: css class selector
                                'widgetItem' => '.item', // required: css class
                                'limit' => 10, // the maximum times, an element can be added (default 999)
                                'min' => 1, // 0 or 1 (default 1)
                                'insertButton' => '.add-item', // css class
                                'deleteButton' => '.remove-item', // css class
                                'model' => $modelsDependency[0],
                                'formId' => 'users-attendlog-form',
                                'formFields' => [
                                    'auditory_id',
                                    'timestamp_received',
                                    'timestamp_over',
                                ],
                            ]); ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center">Аудитория</th>
                                    <th class="text-center">Время выдачи</th>
                                    <th class="text-center">Время сдачи</th>
                                    <th class="text-center">
                                        <?php if (!$readonly): ?>
                                            <button type="button" class="add-item btn btn-success btn-xs"><span
                                                        class="fa fa-plus"></span></button>
                                        <?php endif; ?>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="container-items">
                                <?php foreach ($modelsDependency as $index => $modelDependency): ?>
                                    <tr class="item">
                                        <?php
                                        // necessary for update action.
                                        if (!$modelDependency->isNewRecord) {
                                            echo Html::activeHiddenInput($modelDependency, "[{$index}]id");
                                        }
                                        ?>
                                        <td>
                                            <?php
                                            $field = $form->field($modelDependency, "[{$index}]auditory_id");
                                            echo $field->begin();
                                            ?>
                                            <div class="col-sm-12">
                                                <?= \kartik\select2\Select2::widget(
                                                    [
                                                        'model' => $modelDependency,
                                                        'attribute' => "[{$index}]auditory_id",
                                                        'data' => RefBook::find('auditory_memo_1')->getList(),
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
                                            $field = $form->field($modelDependency, "[{$index}]timestamp_received");
                                            echo $field->begin();
                                            ?>
                                            <div class="col-sm-12">
                                                <?php
                                                $options = [];
                                                if ($modelDependency->isNewRecord) {
                                                    $options = ['value' => Yii::$app->formatter->asDatetime(time())];
                                                }
                                                ?>
                                                <?= \yii\helpers\Html::activeTextInput($modelDependency, "[{$index}]timestamp_received", array_merge($options, ['readonly' => $readonly,  "class" => "form-control js-slab-name"])); ?>
                                                <p class="help-block help-block-error"></p>
                                            </div>
                                            <?= $field->end(); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $field = $form->field($modelDependency, "[{$index}]timestamp_over");
                                            echo $field->begin();
                                            ?>
                                            <div class="col-sm-12">
                                                <?= \yii\helpers\Html::activeTextInput($modelDependency, "[{$index}]timestamp_over", ['class' => 'form-control', 'readonly' => $readonly]); ?>
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
    </div>
</div>
