<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'studyplan-stat-search',
    'validateOnBlur' => false,
])
?>
<div class="studyplan-stat-search">
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

                    <?= Html::submitButton('<i class="fa fa-html5" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'send']); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

