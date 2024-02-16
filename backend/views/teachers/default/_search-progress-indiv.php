<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
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
                <?php
                if (\artsoft\Art::isBackend()) {
                    echo $form->field($model_date, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \artsoft\helpers\RefBook::find('teachers_fio', 1)->getList(),
                        'options' => [
                            'onchange' => 'js: $(this).closest("form").submit()',
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/teachers', 'Teacher'));
                }
                ?>
                <?= $form->field($model_date, "date_in")->widget(DatePicker::class, [
                        'type' => \kartik\date\DatePicker::TYPE_INPUT,
                        'options' => ['placeholder' => ''],
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'MM.yyyy',
                            'autoclose' => true,
                            'minViewMode' => 1,
//                                'todayBtn' => 'linked',
                            'todayHighlight' => true,
                            'multidate' => true,
                            'clearBtn' => true
                        ],
                        'pluginEvents' => ['changeDate' => "function(e){
                           $(e.target).closest('form').submit();
                        }"]
                    ]
                )->label('Месяц и год');
                ?>
                <?= $form->field($model_date, 'subject_key')->widget(\kartik\select2\Select2::class, [
                    'data' => \common\models\education\LessonProgressView::getIndivListForTeachers($modelTeachers->id, $plan_year),
                    'options' => [
                        'onchange' => 'js: $(this).closest("form").submit()',
                        'placeholder' => Yii::t('art', 'Select...'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Предмет');
                ?>

            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>