<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\studygroups\SubjectSect;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studygroups\SubjectSect */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="subject-sect-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'subject-sect-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Html::encode($this->title) ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $form->field($model, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                                [
                                    // 'disabled' => $model->plan_year ? true : $readonly,
                                    'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => $model->isNewRecord ? true : false]
                                    ]
                                ]);
                            ?>

                            <?= $form->field($model, 'course')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                                'options' => [
                                    // 'disabled' => $model->course ? true : $readonly,
                                    'placeholder' => Yii::t('art/guide', 'Select Course...'),
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/guide', 'Course'));
                            ?>
                            <?php $model->union->getSubjectByProgramList();?>
                            <?= $form->field($model, 'union_id')->widget(\kartik\select2\Select2::class, [
                                'id' => 'union_id',
                                'data' => \artsoft\helpers\RefBook::find('union_name', $model->isNewRecord ? \common\models\education\EducationUnion::STATUS_ACTIVE : '')->getList(),
                                'options' => [

                                    // 'disabled' => $readonly,
                                    'placeholder' => Yii::t('art', 'Select...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],

                            ]); ?>
                            <?= $form->field($model, 'subject_cat_id')->widget(\kartik\select2\Select2::class, [
                                    'id' => 'subject_cat_id',
                                    'data' => \artsoft\helpers\RefBook::find('subject_category_name', $model->isNewRecord ? \common\models\subject\SubjectCategory::STATUS_ACTIVE : '')->getList(),
                                    'options' => [

                                        // 'disabled' => $readonly,
                                        'placeholder' => Yii::t('art', 'Select...'),
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],

                            ]); ?>

<!--                            --><?//= $form->field($model, 'subject_id')->widget(\kartik\depdrop\DepDrop::class, [
//                                    'data' => $model->getSubjectByCategory($model->subject_cat_id),
//                                    'options' => [
//                                        'prompt' => Yii::t('art', 'Select...'),
//                                       // 'disabled' => $readonly,
//                                    ],
//                                    'pluginOptions' => [
//                                        'depends' => ['subject_cat_id'],
//                                        'placeholder' => Yii::t('art', 'Select...'),
//                                        'url' => \yii\helpers\Url::to(['/education/default/subject', 'id' => $model->id])
//                                    ]
//                                ]); ?>

                        <?= $form->field($model, 'subject_type_id')->textInput() ?>

                        <?= $form->field($model, 'subject_vid_id')->textInput() ?>

                        <?= $form->field($model, 'sect_name')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model, 'studyplan_list')->widget(\kartik\select2\Select2::className(), [
                                'data' => RefBook::find('education_programm_name', $model->isNewRecord ? \common\models\education\EducationProgramm::STATUS_ACTIVE : '')->getList(),
                                'options' => [
                                    // 'disabled' => $readonly,
                                    Yii::t('art/studyplan', 'Select Education Programm...'),
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ])->label(Yii::t('art/studyplan', 'Education Programm'));
                            ?>

                        <?= $form->field($model, 'week_time')->textInput() ?>


                    </div>
                </div>
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
