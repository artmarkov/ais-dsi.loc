<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\user\UserCommon;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'statement-search',
    'validateOnBlur' => false,
])
?>
    <div class="statement-search">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                            [
                                'disabled' => false,
//                                'onchange'=>'js: $(this).closest("form").submit()',
//                                'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' =>  true ],
//                                ],
                            ])->label(Yii::t('art/studyplan', 'Plan Year'));
                        ?>
                        <?= $form->field($model_date, 'teachers_list')->widget(\kartik\select2\Select2::class, [
                            'data' => RefBook::find('teachers_fio',  UserCommon::STATUS_ACTIVE)->getList(),
                            'options' => [
                                'placeholder' => Yii::t('art', 'Select...'),
                                'multiple' => true,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(Yii::t('art/creative', 'Аuthors-performers'));
                        ?>
                        <?= $form->field($model_date, 'subject_type_flag')->checkbox()->label('Учесть внебюджетные часы') ?>

                        <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

