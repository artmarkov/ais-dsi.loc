<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use yii\helpers\Url;

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
    <div class="panel panel-info">
        <div class="panel-heading">
            Параметры фильтрации
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info"></i> Заполните поля, помеченные * и нажмите "Получить данные".',
                        'options' => ['class' => 'alert-info'],
                    ]);
                    ?>
                    <?= $form->field($model_date, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                            'disabled' => false,
                        ])->label(Yii::t('art/studyplan', 'Plan Year'));
                    ?>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($model_date, "education_cat_id")->widget(\kartik\select2\Select2::class, [
                        'data' => RefBook::find('education_cat_short')->getList(),
                        'options' => [
                            'id' => 'education_cat_id',
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/guide', 'Education Cat')); ?>

                    <?= $form->field($model_date, 'programm_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => \common\models\education\EducationProgramm::getProgrammListByName($model_date->education_cat_id),
                        'options' => [
                            'multiple' => true,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'depends' => ['education_cat_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => Url::to(['/education/default/programm'])
                        ]
                    ])->label(Yii::t('art/studyplan', 'Education Programm')); ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= Html::submitButton('<i class="fa fa-arrow-right" aria-hidden="true"></i> Получить данные', ['class' => 'btn btn-primary', 'name' => 'submitAction', 'value' => 'send']); ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

