<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\studyplan\StudyplanView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'studyplan-progress',
    'validateOnBlur' => false,
])
?>
    <div class="studyplan-progress-search">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo \yii\bootstrap\Alert::widget([
                    'body' => '<i class="fa fa-info-circle"></i> Задайте период и нажмите кнопку "Применить".',
                    'options' => ['class' => 'alert-info'],
                ]);
                ?>
                <?= $form->field($model_date, "date_in")->widget(DatePicker::class, [
                        'type' => \kartik\date\DatePicker::TYPE_INPUT,
                        'options' => ['placeholder' => ''],
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'MM.yyyy',
                            'autoclose' => true,
                            'minViewMode' => 1,
                            'todayBtn' => 'linked',
                            'todayHighlight' => true,
                            'orientation' => 'bottom',
                        ],
//                        'pluginEvents' => ['changeDate' => "function(e){
//                                           $(e.target).closest('form').submit();
//                                        }"]
                    ]
                )->label('Дата начала периода'); ?>

                <?= $form->field($model_date, "date_out")->widget(DatePicker::class, [
                        'type' => \kartik\date\DatePicker::TYPE_INPUT,
                        'options' => ['placeholder' => ''],
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'MM.yyyy',
                            'autoclose' => true,
                            'minViewMode' => 1,
                            'todayBtn' => 'linked',
                            'todayHighlight' => true,
                            'orientation' => 'bottom',
                        ],
//                        'pluginEvents' => ['changeDate' => "function(e){
//                                           $(e.target).closest('form').submit();
//                                        }"]
                    ]
                )->label('Дата окончания периода'); ?>
                <?php if (\artsoft\Art::isBackend()): ?>
                    <?= $form->field($model_date, 'studyplan_id')->widget(\kartik\select2\Select2::class, [
                        'data' => StudyplanView::getStudyplanListByPlanYear($model_date->plan_year),
                        'options' => [
//                            'onchange' => 'js: $(this).closest("form").submit()',
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label('Ученик');
                    ?>
                <?php endif; ?>
            </div>
            <div class="panel-footer">
                <div class="form-group btn-group">
                    <?= Html::submitButton('<i class="fa fa-check" aria-hidden="true"></i> Применить', ['class' => 'btn btn-sm btn-primary', 'name' => 'submitAction']); ?>

                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>