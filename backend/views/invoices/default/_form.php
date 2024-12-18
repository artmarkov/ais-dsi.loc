<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\StudyplanInvoices */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="studyplan-invoices-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'studyplan-invoices-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка платежа
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php if (\artsoft\Art::isBackend()): ?>
                        <?= \yii\bootstrap\Alert::widget([
                            'body' => '<i class="fa fa-info"></i> При добавлении нескольких месяцев, используйте шаблон в назначении платежа в виде {month} вместо реального названия месяца.<br/> <b>Например:</b> Вместо "БО МУЗ за февраль", пишите "БО МУЗ за {month}"',
                            'options' => ['class' => 'alert-info'],
                        ]);
                        ?>
                    <?php endif; ?>
                </div>
                <div class="col-sm-12">
                    <?php if ($model->isNewRecord): ?>
                        <?php
                        echo Html::activeHiddenInputList($studyplanIds, 'ids');
                        ?>
                    <?php endif; ?>

                    <?= $form->field($model, 'invoices_id')->dropDownList(\common\models\own\Invoices::getInvoicesList(), [
                        'prompt' => Yii::t('art', 'Select...'),
                        //  'disabled' => $readonly,
                    ]);
                    ?>

                    <?= $form->field($model, 'status')->radioList($model->getStatusList()) ?>

                    <?= $form->field($model, 'direction_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\guidejob\Direction::getDirectionList(),
                        'options' => [
                            'id' => 'direction_id',
                            'disabled' => !$model->isNewRecord,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'teachers_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => \common\models\teachers\Teachers::getTeachersList($model->direction_id),
                        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                        'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                        'options' => [
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'depends' => ['direction_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => Url::to(['/teachers/default/teachers'])
                        ]
                    ]);
                    ?>
                    <?= $form->field($model, 'month_time_fact')->textInput() ?>

                    <?= $form->field($model, 'invoices_tabel_flag')->checkbox() ?>

                    <?= $form->field($model, 'type_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\subject\SubjectType::getTypeList(),
                        'options' => [
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Subject Type'));
                    ?>

                    <?= $form->field($model, 'invoices_summ')->textInput() ?>

                    <?= $form->field($model, 'mat_capital_flag')->checkbox() ?>

                    <?= $form->field($model, 'invoices_reporting_month')->widget(DatePicker::class, [
                            'type' => DatePicker::TYPE_INPUT,
                            'options' => ['placeholder' => ''],
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'format' => 'MM.yyyy',
                                'autoclose' => !$model->isNewRecord,
                                'minViewMode' => 1,
//                                'todayBtn' => 'linked',
                                'todayHighlight' => true,
                                'multidate' => $model->isNewRecord,
                            ]
                        ]
                    ); ?>

                    <?= $form->field($model, 'invoices_app')->textInput(['maxlength' => true])->hint(''); ?>

                    <?= $form->field($model, 'invoices_rem')->textInput(['maxlength' => true]) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::exitButton(); ?>
                <?= \artsoft\helpers\ButtonHelper::saveButton('submitAction', 'saveexit', 'Save & Exit', 'btn-md js-validate'); ?>
                <?= $model->isNewRecord ? null : \artsoft\helpers\ButtonHelper::deleteButton(); ?>

            </div>
            <?php if (!$model->isNewRecord): ?>
                <div class="pull-right">
                    <div class="form-group btn-group">
                        <?= Html::a('<i class="fa fa-file-word-o" aria-hidden="true"></i> Сформировать квитанцию',
                            ['/invoices/default/make-invoices', 'id' => $model->id],
                            ['class' => 'btn btn-info']); ?>

                    </div>
                </div>
            <?php endif; ?>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
        $('select[name="StudyplanInvoices[invoices_id]"]').click(function(){
         // console.log($(this).val());
        if($(this).val() === '1000') {
            document.getElementsByClassName("js-validate")[0].removeAttribute("data-confirm");
        } else if($(this).val() !== '') {
         document.getElementsByClassName("js-validate")[0].setAttribute("data-confirm", "Вы уверены в выборе Вида платежа?");
        }
        });

JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>