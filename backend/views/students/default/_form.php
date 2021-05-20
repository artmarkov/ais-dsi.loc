<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use lo\widgets\modal\ModalAjax;
use yii\widgets\MaskedInput;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\students\Student */
/* @var $userCommon \common\models\user\UserCommon */
/* @var $modelsDependence \common\models\students\StudentDependence */
/* @var $readonly */
/* @var $form artsoft\widgets\ActiveForm */
$this->registerJs(<<<JS
$( ".add-item" ).click(function(){ // задаем функцию при нажатиии на элемент <button>
	    $( "#student-form" ).submit(); // вызываем событие submit на элементе <form>
	  });
JS
    , \yii\web\View::POS_END);
$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Представитель: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Представитель: " + (index + 1))
    });
});
';

$this->registerJs($js);

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
    , \yii\web\View::POS_END);
?>

<div class="student-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'student-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
    ])

    ?>
    <?php \yii\widgets\Pjax::begin([
        'id' => 'grid-parent-pjax',
        'timeout' => 5000,
    ]);
    ?>
    <div class="panel">
        <div class="panel-heading">
            Информация об ученике
            <?php if (!$userCommon->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/students/default/history', 'id' => $model->id]); ?></span>
                <?php $user_id = RefBook::find('students_users')->getValue($model->id); ?>
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
            <?= $form->field($model, 'position_id')->dropDownList(\common\models\students\StudentPosition::getPositionList(), [
                'prompt' => Yii::t('art/student', 'Select Position...'),
                'id' => 'position_id',
                'disabled' => $readonly
            ])->label(Yii::t('art/student', 'Position'));
            ?>

            <?= $this->render('/user/_form', ['form' => $form, 'model' => $userCommon, 'readonly' => $readonly]) ?>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Документ
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'sert_name')->dropDownList(\common\models\students\Student::STUDENT_DOC, [
                                'disabled' => $readonly,
                                'options' => [
                                    'birth_cert' => ['selected' => true]
                                ]
                            ]) ?>
                            <?= $form->field($model, 'sert_series')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'sert_num')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'sert_organ')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'sert_date')->widget(MaskedInput::className(), ['mask' => Yii::$app->settings->get('reading.date_mask')])->widget(DatePicker::classname(), ['disabled' => $readonly]); ?>
                            <?php if (!$model->isNewRecord) : ?>
                                <div class="form-group field-student-attachment">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="student-attachment">Скан документа</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'pluginOptions' => ['theme' => 'explorer'], 'disabled' => $readonly]) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

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
                'model' => $modelsDependence[0],
                'formId' => 'student-form',
                'formFields' => [
                    'relation_id',
                    'parent_id',
                    'signer_flag',
                ],
            ]); ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Сведения о родителях(официальных представителях)
                </div>
                <div class="panel-body">
                    <div class="container-items"><!-- widgetBody -->
                        <?php foreach ($modelsDependence as $index => $modelDependence): ?>
                            <div class="item panel panel-info"><!-- widgetItem -->
                                <div class="panel-heading">
                                    <span class="panel-title-activities">Представитель: <?= ($index + 1) ?></span>
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
                                        'prompt' => Yii::t('art/teachers', 'Select Relations...'),
                                       // 'id' => 'relation_id'
                                    ]);
                                    ?>
                                    <?php
                                    $JSSelect = <<<EOF
        function(e) {
         console.log('select2:select', e.params.data);
          //$( "#student-form" ).submit(); 
         if(e.params.data.id == '0') {
         console.log('New');
         var elems = document.getElementsByClassName("click");
        elems[0].click();
         //window.open('/admin/parents/create');
         }
        
}
EOF;


                                    ?>




                                    <?= $form->field($modelDependence, "[{$index}]parent_id")->widget(\kartik\select2\Select2::class, [
                                        'data' => ['0' => '--Новая запись--'] + RefBook::find('parents_fullname', $model->isNewRecord ? \common\models\user\UserCommon::STATUS_ACTIVE : '')->getList(),
//                                      'data' => RefBook::find('parents_fullname', $model->isNewRecord ? \common\models\user\UserCommon::STATUS_ACTIVE : '')->getList(),
                                        'options' => [
                                            'disabled' => $readonly,
                                            'placeholder' => Yii::t('art/parents', 'Select Parents...'),
                                            'multiple' => false,
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                        'pluginEvents' => [
                                            "select2:select" => new \yii\web\JsExpression($JSSelect),
                                        ]
                                    ]);
                                    ?>


                                    <?= $form->field($modelDependence, "[{$index}]signer_flag")->checkbox();
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
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
<?php \yii\widgets\Pjax::end();?>
    <?php ActiveForm::end(); ?>
</div>
<?php
echo ModalAjax::widget([
    'id' => 'createParent',
    'header' => 'Новая запись',
    'toggleButton' => [
        'label' => 'Новая запись',
        'class' => 'click',
        'style' => 'display:none;'
    ],
//                                        'selector' => '.select2',
    'url' => \yii\helpers\Url::to(['/students/default/create-parent', 'id' => $model->id]), // Ajax view with form to load
    'ajaxSubmit' => true, // Submit the contained form as ajax, true by default
    // ... any other yii2 bootstrap modal option you need
    'size' => ModalAjax::SIZE_LARGE,
    //'options' => ['class' => 'header-primary'],
    'autoClose' => true,
    'pjaxContainer' => '#grid-parent-pjax',
    'events' => [
        ModalAjax::EVENT_MODAL_SHOW => new \yii\web\JsExpression("
                                            function(event, data, status, xhr, selector) {
                                                 console.log('EVENT_MODAL_SHOW');
                                            }
                                       "),
        ModalAjax::EVENT_MODAL_SUBMIT => new \yii\web\JsExpression("
                                            function(event, data, status, xhr, selector) {
                                                console.log('EVENT_MODAL_SUBMIT');
                                            }
                                        "),
        ModalAjax::EVENT_MODAL_SHOW_COMPLETE => new \yii\web\JsExpression("
                                            function(event, xhr, textStatus) {
                                                 console.log('EVENT_MODAL_SHOW_COMPLETE');
                                            }
                                        "),
        ModalAjax::EVENT_MODAL_SUBMIT_COMPLETE => new \yii\web\JsExpression("
                                            function(event, xhr, textStatus) {
                                            $('#createParent').modal('hide');
                                                   console.log('EVENT_MODAL_SUBMIT_COMPLETE');
                                                   console.log(xhr);
                                                   console.log(event);
                                                   console.log(xhr.responseJSON.ids);
                                                   console.log(xhr.responseJSON.title);
                                                   var s2 = $('#studentdependence-1-parent_id');
                                                    d = $(s2).select2('data'); 
                                                   console.log(d);
                                                      $(s2).select2('close');
                                                      d.push({id: xhr.responseJSON.ids, text: xhr.responseJSON.title}); 
                                                      $(s2).select2('data', d);
                                            }
                                        ")
    ]
]);
?>