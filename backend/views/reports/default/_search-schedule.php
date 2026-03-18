<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
/* @var $model_date */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'teachers-schedule-search',
    'validateOnBlur' => false,
])
?>
<div class="teachers-schedule-search">
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    if (\artsoft\Art::isBackend()) {
                        echo $form->field($model_date, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \common\models\history\TeachersHistory::getTeachersList($model_date->plan_year),
                            'options' => [
                                'id' => 'teachers_id',
//                                    'onchange'=>'js: $(this).closest("form").submit()',
                                'placeholder' => Yii::t('art', 'Select...'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label(Yii::t('art/teachers', 'Teacher'));
                    }
                    ?>
                    <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                            'disabled' => false,
                            'onchange' => 'js: $(this).closest("form").submit()',
                            'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => true],
                            ],
                        ])->label(Yii::t('art/studyplan', 'Plan Year'));
                    ?>

                    <?= $form->field($model_date, "direction_flag")->checkbox()->label('Дополнительные параметры фильтрации'); ?>

                    <div id="direction_block">
                        <?= $form->field($model_date, 'direction_id')->widget(\kartik\depdrop\DepDrop::class, [
                            'data' => \common\models\teachers\TeachersActivity::getDirectionListForTeachers($model_date->teachers_id),
                            'options' => [
                                'id' => 'direction_id',
                                'placeholder' => Yii::t('art', 'Select...'),
                            ],
                            'pluginOptions' => [
                                'depends' => ['teachers_id'],
                                'placeholder' => Yii::t('art', 'Select...'),
                                'url' => Url::to(['/teachers/default/direction'])
                            ],
                        ])->label(Yii::t('art/teachers', 'Name Direction'));
                        ?>
                        <?= $form->field($model_date, 'direction_vid_id')->widget(\kartik\depdrop\DepDrop::class, [
                            'data' => \common\models\teachers\TeachersActivity::getDirectionVidListForTeachers($model_date->teachers_id, $model_date->direction_id),
                            'options' => [
                                'placeholder' => Yii::t('art', 'Select...'),
                            ],
                            'pluginOptions' => [
                                'depends' => ['teachers_id', 'direction_id'],
                                'placeholder' => Yii::t('art', 'Select...'),
                                'url' => Url::to(['/teachers/default/direction-vid'])
                            ]
                        ])->label(Yii::t('art/teachers', 'Name Direction Vid'));
                        ?>
                        <?= $form->field($model_date, 'subject_type_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \artsoft\helpers\RefBook::find('subject_type_name')->getList(),
                            'options' => [
                                'id' => 'subject_type_id',
                                'placeholder' => Yii::t('art', 'Select...'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],

                        ])->label('Тип занятий'); ?>
                    </div>

                    <?= Html::submitButton('<i class="fa fa-html5" aria-hidden="true"></i> Получить данные в Html', ['class' => 'btn btn-info', 'name' => 'submitAction', 'value' => 'send']); ?>
                    <?= Html::submitButton('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Выгрузить в Excel', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'excel']); ?>
                    <?= Html::submitButton('<i class="fa fa-copy" aria-hidden="true"></i> Создать документ в папке "Документы"', ['class' => 'btn btn-default', 'name' => 'submitAction', 'value' => 'doc']); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
// Показ модуля деятельности
$('input[type=checkbox][name="DynamicModel[direction_flag]"]').prop('checked') ? $('#direction_block').show() : $('#direction_block').hide();
$('input[type=checkbox][name="DynamicModel[direction_flag]"]').click(function() {
$(this).prop('checked') ? $('#direction_block').show() : $('#direction_block').hide();
});

JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);
?>
