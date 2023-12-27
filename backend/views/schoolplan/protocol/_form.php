<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\studyplan\StudyplanView;
use common\models\teachers\Teachers;
use common\models\user\UserCommon;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanProtocol */
/* @var $form artsoft\widgets\ActiveForm */
?>

    <div class="protocol-form">

        <?php
        $form = ActiveForm::begin([
            'id' => 'protocol-form',
            'validateOnBlur' => false,
        ])
        ?>

        <div class="panel">
            <div class="panel-heading">
               Аттестационная карточка
                <?php if (!$model->isNewRecord): ?>
                    <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <div class="col-sm-12">
                    <div class="row">
                        <?php
                        if ($model->isNewRecord) {
                            echo Html::activeHiddenInput($model, "schoolplan_id");
                        }
                        ?>
                        <?= $form->field($model, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                            'data' => $model->schoolplan->getExecutorsList(),
                            'options' => [
                                'id' => 'teachers_id',
                                'disabled' => $model->schoolplan->isExecutors() ? true : $readonly,
                                'placeholder' => Yii::t('art/teachers', 'Select Teacher...'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(Yii::t('art/teachers', 'Teacher'));
                        ?>

                        <?= $form->field($model, 'studyplan_id')->widget(\kartik\depdrop\DepDrop::class, [
                            'data' => \common\models\teachers\TeachersLoadStudyplanView::getStudyplanListByTeachers($model->teachers_id, $plan_year),
                            'options' => [
                                'id' => 'studyplan_id',
                                'disabled' => $readonly,
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => false,
                            ],
                            'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'pluginOptions' => [
                                'depends' => ['teachers_id'],
                                'placeholder' => Yii::t('art', 'Select...'),
                                'url' => Url::to(['/schoolplan/default/studyplan', 'plan_year' => $plan_year])
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
                            'data' => \common\models\schoolplan\SchoolplanProtocol::getThematicItemsByStudyplanSubject($model->studyplan_subject_id),
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

                        <?= $form->field($model, 'resume')->textarea(['rows' => 3, 'maxlength' => true, 'disabled' => $readonly]) ?>
                    </div>

                </div>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= !$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : (\artsoft\Art::isBackend() ? \artsoft\helpers\ButtonHelper::viewButtons($model) : \artsoft\helpers\ButtonHelper::exitButton()); ?>
                </div>
                <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

