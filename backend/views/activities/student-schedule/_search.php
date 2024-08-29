<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'calendar',
    'validateOnBlur' => false,
])
?>
    <div class="calendar-search">
        <div class="panel panel-default">
            <div class="panel-body">
                <?= $form->field($model_date, 'student_id')->widget(\kartik\select2\Select2::className(), [
                    'data' => \common\models\activities\ActivitiesStudyplanView::getStudentList(),
                    'options' => [
                        'placeholder' => Yii::t('art', 'Select...'),
                        'disabled' => false,
                        'onchange' => 'js: $(this).closest("form").submit()',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Ученик');
                ?>

            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>