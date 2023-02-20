<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use yii\helpers\Url;

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
            Карточка платежа: <?= $model->studyplan->student->fullName?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                   <!-- --><?php
/*                    foreach ($studyplanIds as $index => $studyplan_id) {
                        echo \yii\helpers\Html::activeHiddenInput($model, "[{$index}]studyplan_id");

                    }
                    */?>
                    <?php
                    echo \yii\helpers\Html::activeHiddenInput($model, 'studyplan_id');
                    ?>
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

                    <?= $form->field($model, 'invoices_app')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'invoices_rem')->textInput(['maxlength' => true]) ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
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
