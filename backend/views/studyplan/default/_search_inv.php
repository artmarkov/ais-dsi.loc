<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use artsoft\models\User;
use common\models\studyplan\StudyplanView;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'studyplan-invoices-search',
    'validateOnBlur' => false,
])
?>
    <div class="studyplan-invoices-search">
        <div class="panel">
            <div class="panel-heading">
                Счета за обучение
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php if (\artsoft\Art::isFrontend() && !User::hasRole(['teacher','department'])): ?>
                            <?= $form->field($model_date, 'studyplan_id')->dropDownList(StudyplanView::getStudyplanList($id),
                                [
                                    'disabled' => false,
                                    'onchange' => 'js: $(this).closest("form").submit()',
                                ])->label('Планы ученика');
                            ?>
                        <?php endif; ?>

                        <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                            [
                                'disabled' => false,
                                'onchange'=>'js: $(this).closest("form").submit()',
//                                'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' =>  true ],
//                                ],
                            ])->label(Yii::t('art/studyplan', 'Plan Year'));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$('form').on('reset', function(e) {
console.log(e);

});
JS;
$this->registerJs($js);
