<?php
use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\entrant\Entrant;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\Entrant */
/* @var $form artsoft\widgets\ActiveForm */

$js = <<<JS
    function toggleEntrant(value) {
      if (value === '1'){
          $('.dec1').show();
          $('.dec2').hide();
      } else if (value === '2') {
          $('.dec1').hide();
          $('.dec2').show();
      } else  {
          $('.dec1').hide();
          $('.dec2').hide();
      }
    }
    toggleEntrant($('input[name="Entrant[decision_id]"]:checked').val());
    $('input[name="Entrant[decision_id]"]').click(function(){
       toggleEntrant($(this).val());
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);

?>

<div class="applicants-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'applicants-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка поступающего
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    // necessary for update action.
                    if (!$model->isNewRecord) {
                        echo Html::activeHiddenInput($model, "comm_id");
                    }
                    ?>
                    <?= $form->field($model, 'student_id')->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('students_fullname')->getList(),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                    <?= $form->field($model, 'group_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\entrant\Entrant::getCommGroupList($model->comm_id),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'subject_list')->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('subject_name', $model->isNewRecord ? \common\models\subject\Subject::STATUS_ACTIVE : '')->getList(),
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

                    <?= $form->field($model, 'last_experience')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'status')->dropDownList(Entrant::getStatusList(), ['disabled' => $readonly]) ?>

                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Результаты испытаний
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            ...
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Решение комиссии
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'decision_id')->radioList(Entrant::getDecisionList(),  ['itemOptions' => ['disabled' => $readonly]]) ?>
                        </div>
                        <div class="dec2 col-sm-12">
                            <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="dec1 col-sm-12">
                            <?= $form->field($model, 'unit_reason_id')->textInput() ?>

                            <?= $form->field($model, 'plan_id')->textInput() ?>

                            <?= $form->field($model, 'course')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\helpers\ArtHelper::getCourseList(),
                                'options' => [
                                    'disabled' => $readonly,
                                    'placeholder' => Yii::t('art/guide', 'Select Course...'),
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>

                            <?= $form->field($model, 'type_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \common\models\subject\SubjectType::getTypeList(),
                                'options' => [
                                    'disabled' => $readonly,
                                    'placeholder' => Yii::t('art', 'Select...'),
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
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
