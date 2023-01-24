<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSect */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $readonly */

?>
    <div class="subject-sect-form">

<?php
$form = ActiveForm::begin([
    'fieldConfig' => [
        'inputOptions' => ['readonly' => $readonly]
    ],
    'id' => 'subject-sect-form',
    'validateOnBlur' => false,
])
?>
    <div class="panel">
        <div class="panel-heading">
            Карточка группы
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model, 'programm_list')->widget(\kartik\select2\Select2::className(), [
                        'data' => RefBook::find('education_programm_short_name', $model->isNewRecord ? \common\models\education\EducationProgramm::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'id' => 'programm_list',
                            'multiple' => true,
                            'placeholder' =>  Yii::t('art/studyplan', 'Select Education Programm...'),
                        ],
                        'pluginOptions' => [
                            'disabled' => $readonly,
                            'allowClear' => true
                        ]
                    ])->label(Yii::t('art/studyplan', 'Education Programm'));
                    ?>

                    <?= $form->field($model, 'subject_cat_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\RefBook::find('subject_category_name', $model->isNewRecord ? \common\models\education\EducationUnion::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'id' => 'subject_cat_id',
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'disabled' => $readonly,
                            'allowClear' => true
                        ],

                    ]); ?>

                    <?= $form->field($model, 'subject_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => $model::getSubjectForUnionAndCat($model->programm_list, $model->subject_cat_id),
                        'options' => [
                            'prompt' => Yii::t('art', 'Select...'),
                            'disabled' => $readonly,
                        ],
                        'pluginOptions' => [
                            'depends' => ['programm_list', 'subject_cat_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => \yii\helpers\Url::to(['/sect/default/subject'])
                        ]
                    ]); ?>

                    <?= $form->field($model, 'sect_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'subject_vid_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\subject\SubjectVid::getVidListGroup(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],

                    ]); ?>

                    <?= $form->field($model, 'subject_type_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\subject\SubjectType::getTypeList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Subject Type'));
                    ?>
                    <?= $form->field($model, 'sub_group_qty')->widget(kartik\touchspin\TouchSpin::class, [
                        'disabled' => $readonly,
                        'pluginOptions' => [
                            'min' => $model->sub_group_qty ?? 0,
                            'max' => 15,
                        ]]);
                    ?>
                    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'maxlength' => true]) ?>

                    <?php
                    $options = ['options' => ['style' => ($model->course_flag == 1) ? 'display:block' : 'display:none']];
                    ?>
                    <?= $form->field($model, 'course_flag')->checkbox(['disabled' => $readonly]) ?>

                    <?= $form->field($model, 'term_mastering', $options)->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\ArtHelper::getTermList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])
                    ?>

                    <?= $form->field($model, 'class_index', $options)->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'status')->dropDownList(\common\models\subjectsect\SubjectSect::getStatusList(), [
                        'disabled' => $readonly
                    ]) ?>

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
<?php
$js = <<<JS
    function toggle(field) {
       if ($(field).is(':checked') ) {
        $(".field-subjectsect-class_index").show();
        $(".field-subjectsect-term_mastering").show();
        } else {
            $(".field-subjectsect-class_index").hide();
            $(".field-subjectsect-term_mastering").hide();
        }
    }
    $('input[name="SubjectSect[course_flag]"]').on('click', function () {
         // console.log(this);
       toggle(this);
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>