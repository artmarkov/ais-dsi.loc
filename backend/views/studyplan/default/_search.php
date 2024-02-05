<?php

use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\teachers\Teachers;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
/* @var $teachers_id */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'studyplan-search',
    'validateOnBlur' => false,
])
?>
    <div class="studyplan-search">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                            [
                                'disabled' => false,
                                'onchange'=>'js: $(this).closest("form").submit()',
//                                'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' =>  true ],
//                                ],
                            ])->label(Yii::t('art/studyplan', 'Plan Year'));
                        ?>
                        <?php if(!(\artsoft\Art::isFrontend() && User::hasRole(['student']))):?>
                        <?= $form->field($model_date, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                                'data' => \artsoft\Art::isFrontend() ? Teachers::getTeachersListForTeacher($teachers_id) : \artsoft\helpers\RefBook::find('teachers_fullname', 1)->getList(),
                                'options' => [
                                    'onchange'=>'js: $(this).closest("form").submit()',
                                    'placeholder' => Yii::t('art', 'Select...'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label(Yii::t('art/teachers', 'Teacher'));
                        ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

