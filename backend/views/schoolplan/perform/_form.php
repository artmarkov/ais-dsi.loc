<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\studyplan\StudyplanView;
use common\models\user\UserCommon;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanPerform */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="schoolplan-perform-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'schoolplan-perform-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка выполнения мероприятия
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    if ($model->isNewRecord) {
                        echo Html::activeHiddenInput($model, "schoolplan_id");
                    }
                    ?>
                    <?= $form->field($model, 'studyplan_id')->widget(\kartik\select2\Select2::class, [
                        'data' => StudyplanView::getStudyplanListByPlanYear($plan_year),
                        'showToggleAll' => false,
                        'options' => [
                            'id' => 'studyplan_id',
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);
                    ?>
                    <?= $form->field($model, 'studyplan_subject_id')->widget(\kartik\depdrop\DepDrop::className(), [
                        'data' => \common\models\studyplan\Studyplan::getStudyplanSubjectListByStudyplan($model->studyplan_id),
                        'options' => [
                            'id' => 'studyplan_subject_id',
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                        'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                        'pluginOptions' => [
                            'depends' => ['studyplan_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => Url::to(['/schoolplan/default/studyplan-subject'])
                        ]
                    ]);
                    ?>
                    <?= $form->field($model, 'thematic_items_list')->widget(\kartik\depdrop\DepDrop::className(), [
                        'data' => \common\models\schoolplan\SchoolplanPerform::getThematicItemsByStudyplanSubject($model->studyplan_subject_id),
                        'options' => [

                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                        'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                        'pluginOptions' => [
                            'depends' => ['studyplan_subject_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => Url::to(['/schoolplan/default/studyplan-thematic'])
                        ]
                    ]);
                    ?>
                    <?= $form->field($model, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('teachers_fio', $model->isNewRecord ? UserCommon::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art/teachers', 'Select Teacher...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/teachers', 'Teachers'));
                    ?>

                    <?= $form->field($model, 'lesson_mark_id')->widget(\kartik\select2\Select2::class, [
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

                    <?= $form->field($model, 'winner_id')->widget(\kartik\select2\Select2::class, [
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
                    <?= $form->field($model, 'resume')->textarea(['rows' => 3, 'maxlength' => true]) ?>
                </div>
                <?php if (!$model->isNewRecord): ?>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label class="control-label">Загруженные материалы(сканы диплома, грамоты)</label>
                            </div>
                            <div class="col-sm-9">
                                <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'pluginOptions' => ['theme' => 'explorer'], 'disabled' => $readonly]) ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                <?php endif; ?>
                <div class="row">
                    <?= $form->field($model, 'status_exe')->widget(\kartik\select2\Select2::class, [
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

                    <?= $form->field($model, 'status_sign')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\schoolplan\SchoolplanProtocolItems::getStatusSignList(),
                        'showToggleAll' => false,
                        'options' => [
                            'disabled' => true,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],

                    ]);
                    ?>

                    <?= $form->field($model, 'signer_id')->widget(\kartik\select2\Select2::class, [
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
                <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model); ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>