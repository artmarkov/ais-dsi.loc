<?php

use artsoft\widgets\ActiveForm;
use common\models\teachers\Teachers;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use wbraganca\dynamicform\DynamicFormWidget;
use common\models\user\UserCommon;
use common\models\own\Department;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\Teachers */
/* @var $userCommon UserCommon */
/* @var $modelsActivity \common\models\teachers\TeachersActivity */
/* @var $readonly */
/* @var $form artsoft\widgets\ActiveForm */

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Деятельность: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Деятельность: " + (index + 1))
    });
});
';

$this->registerJs($js);

$JSUnselect = <<<EOF
        function(e) {
         console.log('select2:unselect', e.params.data);
         var bonus0 =  parseFloat(document.getElementById('teachers-bonus_summ').value);
         if (isNaN(bonus0) == true) bonus0 = 0;
         
         $.ajax({
            url: '/admin/teachers/default/select',
            type: 'POST',
            data: {
                id: e.params.data.id 
            },
            success: function (bonus) {
             var bonus = bonus0 - parseFloat(bonus);
              if (bonus < 0) bonus = 0;
             document.getElementById('teachers-bonus_summ').value = bonus;
            },
            error: function () {
                alert('Error!!!');
            }
        });
}
EOF;
$JSSelect = <<<EOF
        function(e) {
         console.log('select2:select', e.params.data);
         var bonus0 =  parseFloat(document.getElementById('teachers-bonus_summ').value);
         if (isNaN(bonus0) == true) bonus0 = 0;
         
         $.ajax({
            url: '/admin/teachers/default/select',
            type: 'POST',
            data: {
                id: e.params.data.id 
            },
            success: function (bonus) {
            var bonus = bonus0 + parseFloat(bonus);
             document.getElementById('teachers-bonus_summ').value = bonus;
            },
            error: function () {
                alert('Error!!!');
            }
        });
}
EOF;

?>

<div class="teachers-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'teachers-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
    ])

    ?>
    <div class="panel">
        <div class="panel-heading">
            Информация о преподавателе
            <?php if (!$userCommon->isNewRecord):?>
            <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/teachers/default/history', 'id' => $model->id]); ?></span>
               <?php $user_id = RefBook::find('teachers_users')->getValue($model->id); ?>
                <?php if ($user_id): ?>
                    <span class="pull-right"> <?= Html::a('<i class="fa fa-user-o" aria-hidden="true"></i> Регистрационные данные',
                            ['user/default/update', 'id' => $user_id],
                            [
                                'target' => '_blank',
                                'class' => 'btn btn-default ',
                            ]
                        ); ?>
                    </span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="panel-body">

            <?= $this->render('@backend/views/user/_form', ['form' => $form, 'model' => $userCommon, 'readonly' => $readonly]) ?>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Должностные характеристики
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            echo $form->field($model, 'position_id')->dropDownList(common\models\guidejob\Position::getPositionList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Position...'),
                                'id' => 'position_id',
                                'disabled' => $readonly,
                            ])->label(Yii::t('art/teachers', 'Name Position'));
                            ?>

                            <?php
                            echo $form->field($model, 'level_id')->dropDownList(common\models\guidejob\Level::getLevelList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Level...'),
                                'id' => 'level_id',
                                'disabled' => $readonly,
                            ])->label(Yii::t('art/teachers', 'Name Level'));
                            ?>
                            <?= $form->field($model, 'tab_num')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'year_serv')->textInput() ?>
                            <?= $form->field($model, 'date_serv')->widget(DatePicker::class,['disabled' => $readonly])->label(Yii::t('art/teachers', 'For date')); ?>
                            <?= $form->field($model, 'year_serv_spec')->textInput() ?>
                            <?= $form->field($model, 'date_serv_spec')->widget(DatePicker::class,['disabled' => $readonly])->label(Yii::t('art/teachers', 'For date')); ?>
                            <?= $form->field($model, 'department_list')->widget(\kartik\select2\Select2::className(), [
                                'data' => Department::getDepartmentList(),
                                'options' => [
                                    'disabled' => $readonly,
                                    'placeholder' => Yii::t('art/teachers', 'Select Department...'),
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/guide', 'Department'));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 4, // the maximum times, an element can be added (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsActivity[0],
                'formId' => 'teachers-form',
                'formFields' => [
                    'work_id',
                    'direction_id',
                    'stake_id',
                ],
            ]); ?>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Сведения о трудовой деятельности

                </div>
                <div class="panel-body">
                    <?= \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-warning"></i> При выборе вида работы используйте следующее правило. Выбирайте сначала основной вид работы.',
                        'options' => ['class' => 'alert-warning'],
                    ]);
                    ?>
                    <div class="container-items"><!-- widgetBody -->
                        <?php foreach ($modelsActivity as $index => $modelActivity): ?>
                            <div class="item panel panel-info"><!-- widgetItem -->
                                <div class="panel-heading">
                                    <span class="panel-title-activities">Деятельность: <?= ($index + 1) ?></span>
                                    <?php if (!$readonly): ?>
                                        <div class="pull-right">
                                            <button type="button" class="remove-item btn btn-default btn-xs">удалить
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    // necessary for update action.
                                    if (!$modelActivity->isNewRecord) {
                                        echo Html::activeHiddenInput($modelActivity, "[{$index}]id");
                                    }
                                    ?>
                                    <?= $form->field($modelActivity, "[{$index}]work_id")->dropDownList(common\models\guidejob\Work::getWorkList(), [
                                        'prompt' => Yii::t('art/teachers', 'Select Work...'),
                                        'id' => 'work_id'
                                    ])->label(Yii::t('art/teachers', 'Name Work'));
                                    ?>
                                    <?= $form->field($modelActivity, "[{$index}]direction_id")->dropDownList(\common\models\guidejob\Direction::getDirectionList(), [
                                        'prompt' => Yii::t('art/teachers', 'Select Direction...'),
                                        'id' => 'direction_id'
                                    ])->label(Yii::t('art/teachers', 'Name Direction'));
                                    ?>
                                    <?= $form->field($modelActivity, "[{$index}]stake_id")->dropDownList(\common\models\guidejob\Stake::getStakeList(), [
                                        'prompt' => Yii::t('art/teachers', 'Select Stake...'),
                                        'id' => 'direction_id'
                                    ])->label(Yii::t('art/teachers', 'Name Stake'));
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div><!-- .panel -->
                <?php if (!$readonly): ?>
                    <div class="panel-footer">
                        <div class="form-group btn-group">
                            <button type="button" class="add-item btn btn-success btn-sm pull-right">
                                <i class="glyphicon glyphicon-plus"></i> Добавить
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php DynamicFormWidget::end(); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Сведения о достижениях
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">

                                <?= $form->field($model, 'bonus_list')->widget(\kartik\select2\Select2::className(), [
                                    'data' => \common\models\guidejob\Bonus::getBonusList(),
                                    'showToggleAll' => false,
                                    'options' => [
                                        'disabled' => $readonly,
                                        'placeholder' => Yii::t('art/teachers', 'Select Teachers Bonus...'),
                                        'multiple' => true,
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => false,
                                    ],
                                    'pluginEvents' => [
                                        "select2:select" => new \yii\web\JsExpression($JSSelect),
                                        "select2:unselect" => new \yii\web\JsExpression($JSUnselect),
                                    ],
                                ])->label(Yii::t('art/teachers', 'Teachers Bonus'));
                                ?>

                                <?= $form->field($model, 'bonus_summ')->textInput()->hint('При начальной загрузке Будет учтена сумма бонусов всех выбранных достижений.') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
