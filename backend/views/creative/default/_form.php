<?php

use artsoft\widgets\ActiveForm;
use common\models\creative\CreativeWorks;
use common\models\creative\CreativeCategory;
use artsoft\models\User;
use artsoft\helpers\Html;
use common\models\user\UserCommon;
use kartik\date\DatePicker;
use artsoft\helpers\RefBook;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;


/* @var $this yii\web\View */
/* @var $model common\models\creative\CreativeWorks */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */
/* @var $modelsEfficiency */

$this->registerJs(<<<JS
$( ".add-item" ).click(function(){ // задаем функцию при нажатиии на элемент <button>
	    $( "#creative-works-form" ).submit(); // вызываем событие submit на элементе <form>
	  });
JS
    , \yii\web\View::POS_END);

$js = <<<JS
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Поощрение: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-activities").each(function(index) {
        jQuery(this).html("Поощрение: " + (index + 1))
    });
});


JS;

$this->registerJs($js);

$this->registerJs(<<<JS
function initSelect2Loading(a,b){ initS2Loading(a,b); }
function initSelect2DropStyle(id, kvClose, ev){ initS2ToggleAll(id, kvClose, ev); }
JS
    , \yii\web\View::POS_END);

$this->registerJs(<<<JS
    let today = new Date();
    let dd = String(today.getDate()).padStart(2, '0');
    let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    let yyyy = today.getFullYear();
    today = dd + '.' + mm + '.' + yyyy;
    
    jQuery(".dynamicform_wrapper .kv-tree-input").each(function(index) {
        $("#efficiency_tree" + index).on('treeview:change', function(event, key) {
        $.ajax({
                url: '/admin/efficiency/default/select',
                type: 'POST',
                data: {
                    id: key
                },
                success: function (bonus) {
                   let  p = jQuery.parseJSON(bonus);
                   let date = document.getElementById('teachersefficiency-' + index + '-date_in');
                    document.getElementsByName('TeachersEfficiency[' + index + '][bonus_vid_id]')[p.id].checked = true;
                    document.getElementById('teachersefficiency-' + index + '-bonus').value = p.value;
                    date.value = !date.value ? today : date.value;
                },
                error: function () {
                    alert('Error!!!');  
                }
            });
        });
    });
JS
    , \yii\web\View::POS_END);

$readonly = in_array($model->doc_status, [1, 2]) && \artsoft\Art::isFrontend() ? true : $readonly;
$readonly_dep = \artsoft\Art::isFrontend() ? true : $readonly;

?>

<div class="creative-works-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'creative-works-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
        'enableClientScript' => true, // default
    ])
    ?>
    <?php $teachers_fio_list = RefBook::find('teachers_fio', !$readonly ? UserCommon::STATUS_ACTIVE : '')->getList(); ?>

    <div class="panel">
        <div class="panel-heading">
            Сведения о работе
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'category_id')->dropDownList(CreativeCategory::getCreativeCategoryList(), ['prompt' => '', 'encodeSpaces' => true, 'disabled' => $model->doc_status == 1 ?: $readonly]) ?>

                    <?= $form->field($model, 'name')->textarea(['rows' => '3', 'maxlength' => true, 'disabled' => $readonly]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => '3', 'maxlength' => true, 'disabled' => $readonly]) ?>

                    <?= $form->field($model, 'department_list')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\own\Department::getDepartmentList(),
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
                    <?= $form->field($model, 'teachers_list')->widget(\kartik\select2\Select2::class, [
                        'data' => $teachers_fio_list,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/creative', 'Select performers...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/creative', 'Аuthors-performers'));
                    ?>

                    <?= $form->field($model, 'status')->radioList(CreativeWorks::getStatusList(), ['disabled' => $readonly]) ?>

                    <?= $form->field($model, 'place')->textInput(['maxlength' => true, 'readonly' => $readonly])->hint('Напишите название учреждения и город, где Вы проходили обучение(повышение квалификации или перподготовку)') ?>

                    <?= $form->field($model, 'date')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly])->hint('Укажите дату получения сертификата или оставьте поле пустым, если статус - Запланировано'); ?>

                    <?php /*$form->field($model, 'published_at')->widget(DatePicker::class, ['disabled' => $readonly])->textInput(['autocomplete' => 'off']); */ ?>

                </div>
            </div>

            <?php if (!$model->isNewRecord) : ?>

                <div class="panel panel-info">
                    <div class="panel-heading">
                        Загруженные материалы
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'disabled' => $readonly]) ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'efficiency_flag')->checkbox(['disabled' => $readonly]) ?>
                </div>
            </div>
            <div id="efficiencyOpen">
                <?php if (isset($modelsEfficiency)): ?>
                <?php if (!$model->isNewRecord) : ?>
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 15, // the maximum times, an element can be added (default 999)
                    'min' => $model->efficiency_flag ? 1 : 0, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsEfficiency[0],
                    'formId' => 'creative-works-form',
                    'formFields' => [
                        'efficiency_id',
                        'teachers_id',
                        'bonus_vid_id',
                        'bonus',
                        'date_in',
                    ],
                ]); ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Поощрения за работу
                    </div>
                    <div class="panel-body">
                        <div class="container-items"><!-- widgetBody -->
                            <?php foreach ($modelsEfficiency as $index => $modelEfficiency): ?>
                                <div class="item panel panel-info"><!-- widgetItem -->
                                    <div class="panel-heading">
                                        <span class="panel-title-activities">Поощрение: <?= ($index + 1) ?></span>
                                        <?php if (!$readonly_dep): ?>
                                            <div class="pull-right">
                                                <button type="button" class="remove-item btn btn-default btn-xs">
                                                    удалить
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        // necessary for update action.
                                        if (!$modelEfficiency->isNewRecord) {
                                            echo Html::activeHiddenInput($modelEfficiency, "[{$index}]id");
                                        }
                                        ?>
                                        <?= $form->field($modelEfficiency, "[{$index}]efficiency_id")->widget(\kartik\tree\TreeViewInput::class, [
                                            'options' => [
                                                'disabled' => $readonly_dep,
                                                'id' => "efficiency_tree{$index}",
                                            ],
                                            'query' => \common\models\efficiency\EfficiencyTree::find()->andWhere(['root' => [3, 12]])->addOrderBy('root, lft'),
                                            'dropdownConfig' => [
                                                'input' => ['placeholder' => 'Выберите показатель эффективности...'],
                                            ],
                                            'fontAwesome' => true,
                                            'multiple' => false,
                                            'rootOptions' => [
                                                'label' => '',
                                                'class' => 'text-default'
                                            ],
                                            'childNodeIconOptions' => ['class' => ''],
                                            'defaultParentNodeIcon' => '',
                                            'defaultParentNodeOpenIcon' => '',
                                            'defaultChildNodeIcon' => '',
                                            'childNodeIconOptions' => ['class' => ''],
                                            'parentNodeIconOptions' => ['class' => ''],
                                        ]);
                                        ?>
                                        <?= $form->field($modelEfficiency, "[{$index}]teachers_id")->widget(\kartik\select2\Select2::class, [
                                            'data' => $teachers_fio_list,
                                            'options' => [
                                                'disabled' => $readonly_dep,
                                                'placeholder' => Yii::t('art/teachers', 'Select Teacher...'),
                                                'multiple' => false,
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true
                                            ],
                                        ])->label(Yii::t('art/teachers', 'Teachers'));
                                        ?>
                                        <?= $form->field($modelEfficiency, "[{$index}]bonus_vid_id")->radioList(\common\models\efficiency\EfficiencyTree::getBobusVidList(), ['itemOptions' => ['disabled' => $readonly_dep]]) ?>
                                        <?php
                                        if ($readonly_dep) {
                                            echo Html::activeHiddenInput($modelEfficiency, "[{$index}]bonus_vid_id"); // Костыль для $readonly_dep radioList
                                        }
                                        ?>
                                        <?= $form->field($modelEfficiency, "[{$index}]bonus")->textInput(['maxlength' => true, 'readonly' => $readonly_dep]) ?>
                                        <?= $form->field($modelEfficiency, "[{$index}]date_in")->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly_dep]); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div><!-- .panel -->
                    <?php if (!$readonly_dep): ?>
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
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <hr>
            <div class="row"> <?php
                if (\artsoft\Art::isFrontend()) {
                    echo Html::activeHiddenInput($model, 'author_id');
                } else {
                    echo $form->field($model->loadDefaultValues(), 'author_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\user\UserCommon::getUsersCommonListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            //'minimumInputLength' => 3,
                        ],

                    ]);
                }
                ?>
                <?= $form->field($model->loadDefaultValues(), 'doc_status')->widget(\kartik\select2\Select2::class, [
                    'data' => CreativeWorks::getDocStatusList(),
                    'showToggleAll' => false,
                    'options' => [
                        'disabled' => true,
                        'placeholder' => Yii::t('art', 'Select...'),
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => false,
                        //'minimumInputLength' => 3,
                    ],

                ]);
                ?>
                <?= $form->field($model, 'signer_id')->widget(\kartik\select2\Select2::class, [
                    'data' => User::getUsersByIds(User::getUsersByRole('signerSchoolplan')),
                    'showToggleAll' => false,
                    'options' => [
                        'disabled' => $readonly,
                        'placeholder' => Yii::t('art', 'Select...'),
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => false,
                    ],

                ]);
                ?>
            </div>
            <?php if (!$model->isNewRecord && \artsoft\Art::isBackend() && !$readonly) : ?>
                <div class="row">
                    <hr>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'admin_flag')->checkbox(['disabled' => $readonly])->label('Добавить сообщение') ?>
                        <div id="send_admin_message">
                            <?= $form->field($model, 'admin_message')->textInput()->hint('Введите сообщение для автора работы и нажмите "Отправить на доработку"') ?>
                        </div>

                    </div>
                </div>
                <div class="form-group btn-group">
                    <?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Согласовать', ['class' => 'btn btn-sm btn-success', 'name' => 'submitAction', 'value' => 'approve', 'disabled' => $model->doc_status == 1]); ?>
                    <?= Html::submitButton('<i class="fa fa-send-o" aria-hidden="true"></i> Отправить на доработку', ['class' => 'btn btn-sm btn-default pull-right', 'name' => 'submitAction', 'value' => 'modif']); ?>
                </div>
            <?php endif; ?>
            <?php if (!$model->isNewRecord && \artsoft\Art::isFrontend() && $model->isAuthor()): ?>
                <div class="form-group btn-group">
                    <?= Html::submitButton('<i class="fa fa-arrow-up" aria-hidden="true"></i> Отправить на согласование', ['class' => 'btn btn-sm btn-primary', 'name' => 'submitAction', 'value' => 'send_approve', 'disabled' => in_array($model->doc_status, [1, 2]) ? true : false]); ?>
                    <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Внести изменения', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'make_changes', 'disabled' => in_array($model->doc_status, [0, 3]) ? true : false]); ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?php if (!$model->isNewRecord): ?>

                    <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::exitButton(); ?>
                <?php else: ?>
                    <?= \artsoft\helpers\ButtonHelper::exitButton(); ?>
                    <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Продолжить', ['class' => 'btn btn-sm btn-info', 'name' => 'submitAction', 'value' => 'next']); ?>
                <?php endif; ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$js = <<<JS
function toggle(value) {
    if(value == 1002){
           $('.field-creativeworks-place').show()
          $('.field-creativeworks-date').show()
           $('.field-creativeworks-status').show()
       } else {
           $('.field-creativeworks-place').hide();
          $('.field-creativeworks-date').hide();
           $('.field-creativeworks-status').hide();
       } 
}
toggle($('select[name="CreativeWorks[category_id]"]').find(":selected").val());
    $('select[name="CreativeWorks[category_id]"]').click(function(){
       toggle($(this).find(":selected").val());
     });
 
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);

$js = <<<JS
     // Показ модуля сообщения
    $('input[type=checkbox][name="CreativeWorks[admin_flag]"]').prop('checked') ? $('#send_admin_message').show() : $('#send_admin_message').hide();
    $('input[type=checkbox][name="CreativeWorks[admin_flag]"]').click(function() {
       $(this).prop('checked') ? $('#send_admin_message').show() : $('#send_admin_message').hide();
     });
    // Показ модуля Поощрения за работу
    $('input[type=checkbox][name="CreativeWorks[efficiency_flag]"]').prop('checked') ? $('#efficiencyOpen').show() : $('#efficiencyOpen').hide();
    $('input[type=checkbox][name="CreativeWorks[efficiency_flag]"]').click(function() {
       $(this).prop('checked') ? $('#efficiencyOpen').show() : $('#efficiencyOpen').hide();
     });
  
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);

?>
