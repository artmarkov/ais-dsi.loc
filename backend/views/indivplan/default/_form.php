<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\TeachersPlan */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="teachers-plan-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'teachers-plan-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
           Карточка планирования индивидуальных занятий
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                        [
                            'disabled' => $model->plan_year ? true : $readonly,
                            'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => $model->isNewRecord ? true : false]
                            ]
                        ]);
                    ?>
                    <?= $form->field($model, 'direction_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\guidejob\Direction::getDirectionList(),
                        'options' => [
                            'id' => 'direction_id',
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'teachers_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => \common\models\teachers\Teachers::getTeachersList($model->direction_id),
                        'options' => [
                            'disabled' => $model->teachers_id ? true : $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                        'pluginOptions' => [
                            'depends' => ['direction_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => Url::to(['/teachers/default/teachers'])
                        ]
                    ]);
                    ?>

                    <?= $form->field($model, 'half_year')->dropDownList(\artsoft\helpers\ArtHelper::getHalfYearList(), ['disabled' => $readonly]);?>
                    <?= $form->field($model, "week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList(), ['disabled' => $readonly]) ?>
                    <?= $form->field($model, "week_day")->dropDownList(['' => Yii::t('art/guide', 'Select week day...')] + \artsoft\helpers\ArtHelper::getWeekdayList(), ['disabled' => $readonly]) ?>
                    <?= $form->field($model, "time_plan_in")->textInput()->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "time_plan_out")->textInput()->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList(), ['disabled' => $readonly]) ?>
                    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

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
