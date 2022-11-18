<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelProtocolItems common\models\schoolplan\SchoolplanProtocolItems */
/* @var $model common\models\studyplan\Studyplan */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="schoolplan-protocol-items-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'schoolplan-protocol-items-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">

                <?= $form->field($modelProtocolItems, 'schoolplan_protocol_id')->widget(\kartik\select2\Select2::class, [
                    'data' => $modelProtocolItems->getSchoolplanProtocols(),
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

                <?= $form->field($modelProtocolItems, 'studyplan_subject_id')->widget(\kartik\select2\Select2::class, [
                    'data' => RefBook::find('subject_memo_1', $model->id)->getList(),
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

                <?= $form->field($modelProtocolItems, 'thematic_items_list')->widget(\kartik\select2\Select2::class, [
                    'data' => \common\models\schoolplan\SchoolplanProtocol::getStudyplanThematicItemsList(10010/*$modelProtocolItems->schoolplan_protocol_id*/),
                    'showToggleAll' => false,
                    'options' => [
                        // 'disabled' => $readonly,
                        'placeholder' => Yii::t('art', 'Select...'),
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => false,
                    ],

                ]);

                ?>
                <?= $form->field($modelProtocolItems, 'lesson_mark_id')->widget(\kartik\select2\Select2::class, [
                    'data' => RefBook::find('lesson_mark')->getList(),
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

                <?= $form->field($modelProtocolItems, 'winner_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \common\models\schoolplan\SchoolplanProtocolItems::getWinnerList(),
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
                <?= $form->field($modelProtocolItems, 'resume')->textarea(['rows' => 3, 'maxlength' => true]) ?>
            </div>
            <?php if (!$modelProtocolItems->isNewRecord): ?>
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="control-label">Загруженные материалы(сканы диплома, грамоты)</label>
                        </div>
                        <div class="col-sm-9">
                            <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $modelProtocolItems, 'options' => ['multiple' => true], 'disabled' => $readonly]) ?>
                        </div>
                    </div>
                </div>
                <hr>
            <?php endif; ?>
            <div class="row">
                <?= $form->field($modelProtocolItems, 'status_exe')->widget(\kartik\select2\Select2::class, [
                    'data' => \common\models\schoolplan\SchoolplanProtocolItems::getStatusExeList(),
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

                <?= $form->field($modelProtocolItems, 'status_sign')->widget(\kartik\select2\Select2::class, [
                    'data' => \common\models\schoolplan\SchoolplanProtocolItems::getStatusSignList(),
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

                <?= $form->field($modelProtocolItems, 'signer_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \artsoft\models\User::getUsersListByCategory(['teachers', 'employees']),
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
        </div>
    </div>
    <div class="panel-footer">
        <div class="form-group btn-group">
            <?= \artsoft\helpers\ButtonHelper::submitButtons($modelProtocolItems) ?>
        </div>
        <?= \artsoft\widgets\InfoModel::widget(['model' => $modelProtocolItems]); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>
