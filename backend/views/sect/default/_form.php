<?php

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

                    <?= $form->field($model, 'union_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\RefBook::find('union_name', $model->isNewRecord ? \common\models\education\EducationUnion::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'id' => 'union_id',

                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],

                    ]); ?>

                    <?= $form->field($model, 'subject_cat_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => $model::getSubjectCategoryForUnion($model->union_id),
                        'options' => [
                            'id' => 'subject_cat_id',
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'depends' => ['union_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => \yii\helpers\Url::to(['/sect/default/subject-cat'])
                        ],

                    ]); ?>

                    <?= $form->field($model, 'subject_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => $model::getSubjectForUnionAndCat($model->union_id, $model->subject_cat_id),
                        'options' => [
                            'prompt' => Yii::t('art', 'Select...'),
                            'disabled' => $readonly,
                        ],
                        'pluginOptions' => [
                            'depends' => ['union_id', 'subject_cat_id'],
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

                    <?= $form->field($model, 'course_flag')->checkbox(['disabled' => $readonly]) ?>
                    <?php
                    $options = ['options' => ['style' => ($model->course_flag == 1) ? 'display:block' : 'display:none']];
                    ?>
                    <?= $form->field($model, 'course_list', $options)->widget(\kartik\select2\Select2::className(), [
                        'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

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
        $(".field-subjectsect-course_list").show();
        } else {
            $(".field-subjectsect-course_list").hide();
        }
    }
    $('input[name="SubjectSect[course_flag]"]').on('click', function () {
         // console.log(this);
       toggle(this);
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>