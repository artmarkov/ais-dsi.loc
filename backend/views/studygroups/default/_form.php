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
                            <?= $form->field($model, 'union_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\helpers\RefBook::find('union_name', $model->isNewRecord ? \common\models\education\EducationUnion::STATUS_ACTIVE : '')->getList(),
                                'options' => [
                                'id' => 'union_id',

                                    // 'disabled' => $readonly,
                                    'placeholder' => Yii::t('art', 'Select...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],

                            ]); ?>
<!--                            --><?php //if(!$model->isNewRecord):?>
                            <?= $form->field($model, 'subject_cat_id')->widget(\kartik\depdrop\DepDrop::class, [
                                    'data' =>  $model::getSubjectCategoryForUnion($model->union_id),
                                    'options' => [
                                    'id' => 'subject_cat_id',
                                        // 'disabled' => $readonly,
                                        'placeholder' => Yii::t('art', 'Select...'),
                                    ],
                                    'pluginOptions' => [
                                    'depends' => ['union_id'],
                                        'placeholder' => Yii::t('art', 'Select...'),
                                        'url' => \yii\helpers\Url::to(['/studygroups/default/subject-cat'])
                                    ],

                            ]); ?>

                            <?= $form->field($model, 'subject_vid_id')->widget(\kartik\select2\Select2::class, [
                                'data' =>  RefBook::find('subject_vid_name')->getList(),
                                'options' => [
                                    'id' => 'subject_vid_id',
                                    // 'disabled' => $readonly,
                                    'placeholder' => Yii::t('art', 'Select...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],

                            ]); ?>


                            <?= $form->field($model, 'subject_id')->widget(\kartik\depdrop\DepDrop::class, [
                                    'data' => $model::getSubjectForUnionAndCat($model->union_id, $model->subject_cat_id),
                                    'options' => [
                                        'prompt' => Yii::t('art', 'Select...'),
                                       // 'disabled' => $readonly,
                                    ],
                                    'pluginOptions' => [
                                        'depends' => ['union_id', 'subject_cat_id', 'subject_vid_id'],
                                        'placeholder' => Yii::t('art', 'Select...'),
                                        'url' => \yii\helpers\Url::to(['/studygroups/default/subject'])
                                    ]
                                ]); ?>

<!--                        --><?//= $form->field($model, 'subject_type_id')->dropDownList(\common\models\subject\SubjectType::getTypeList()) ?>

<!--                        --><?//= $form->field($model, 'subject_vid_id')->dropDownList(\common\models\subject\SubjectVid::getVidList()) ?>

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
<!--                            --><?php //endif;?>

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
