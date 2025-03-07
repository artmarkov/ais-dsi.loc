<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\widgets\MaskedInput;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\parents\Parents */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $userCommon common\models\user\UserCommon */
/* @var $userCard common\models\service\UsersCard */
/* @var $modelsDependence \common\models\students\StudentDependence */
/* @var $readonly */
/* @var $form artsoft\widgets\ActiveForm */

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Ученик: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Ученик: " + (index + 1))
    });
});
';
$this->registerJs($js);

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
    , \yii\web\View::POS_END);

$readonlyParents = $readonly;
if(User::hasRole(['parents'])) {
    $readonlyParents = false;
}
?>

<div class="parents-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' =>  User::hasRole(['parents'], false) ? false : $readonly]
        ],
        'id' => 'parents-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
    ])

    ?>

    <div class="panel">
        <div class="panel-heading">
            Информация о родителе (официальном представителе)
            <?php if (!$userCommon->isNewRecord && \artsoft\Art::isBackend()): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
                <?php $user_id = RefBook::find('parents_users')->getValue($model->id); ?>
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
                    Документ
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'sert_name')->dropDownList(\common\models\parents\Parents::PARENT_DOC, [
                                'disabled' => $readonlyParents,
                                'options' => [
                                    'password' => ['selected' => true]
                                ]
                            ]) ?>
                            <?= $form->field($model, 'sert_series')->textInput(['maxlength' => true, 'disabled' => $readonlyParents]) ?>
                            <?= $form->field($model, 'sert_num')->textInput(['maxlength' => true, 'disabled' => $readonlyParents]) ?>
                            <?= $form->field($model, 'sert_organ')->textInput(['maxlength' => true, 'disabled' => $readonlyParents]) ?>
                            <?= $form->field($model, 'sert_date')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::class, ['disabled' => $readonlyParents]); ?>
                            <?= $form->field($model, 'sert_code')->textInput(['maxlength' => true, 'disabled' => $readonlyParents]) ?>
                            <?= $form->field($model,  'sert_country')->textInput(['maxlength' => true, 'disabled' => $readonlyParents]) ?>
                            <?php if(\artsoft\Art::isBackend()): ?>
                                <?php if (!$model->isNewRecord) : ?>
                                    <div class="form-group field-parents-attachment">
                                        <div class="col-sm-3">
                                            <label class="control-label" for="parents-attachment">Скан документа</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'pluginOptions' => ['theme' => 'explorer'], 'disabled' => $readonlyParents]) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(\artsoft\Art::isBackend()): ?>
            <?= $this->render('@backend/views/user/_form_card', ['form' => $form, 'model' => $userCard, 'readonly' => $readonly]) ?>
            <?php endif;?>
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 4, // the maximum times, an element can be added (default 999)
                'min' => 0, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsDependence[0],
                'formId' => 'parents-form',
                'formFields' => [
                    'relation_id',
                    'student_id',
                ],
            ]); ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Сведения о связях
                </div>
                <div class="panel-body">
                    <div class="container-items"><!-- widgetBody -->
                        <?php foreach ($modelsDependence as $index => $modelDependence): ?>
                            <div class="item panel panel-info"><!-- widgetItem -->
                                <div class="panel-heading">
                                    <span class="panel-title-activities">Ученик: <?= ($index + 1) ?></span>
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
                                    if (!$modelDependence->isNewRecord) {
                                        echo Html::activeHiddenInput($modelDependence, "[{$index}]id");
                                    }
                                    ?>
                                    <?= $form->field($modelDependence, "[{$index}]relation_id")->dropDownList(\common\models\guidesys\UserRelation::getRelationList(), [
                                        'prompt' => Yii::t('art/student', 'Select Relations...'),
                                        'disabled' => $readonly
                                    ])->label(Yii::t('art/student', 'Relation'));
                                    ?>

                                    <?= $form->field($modelDependence, "[{$index}]student_id")->widget(\kartik\select2\Select2::class, [
                                        'data' => RefBook::find('students_fullname', $model->isNewRecord ? \common\models\user\UserCommon::STATUS_ACTIVE : '')->getList(),
                                        'options' => [
                                            'disabled' => $readonly,
                                            'placeholder' => Yii::t('art/parents', 'Select Students...'),
                                            'multiple' => false,
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ]
                                    ])->label(Yii::t('art/student', 'Student'));
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
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?php if(\artsoft\Art::isBackend()): ?>
                    <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
                <?php elseif(User::hasRole(['parents'])):?>
                    <?= \artsoft\helpers\ButtonHelper::saveButton();?>
                <?php endif;?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
