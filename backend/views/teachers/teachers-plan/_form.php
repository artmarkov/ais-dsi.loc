<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use common\models\guidejob\Direction;
use common\models\teachers\TeachersPlan;
use artsoft\helpers\Html;
use common\models\user\UserCommon;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\TeachersPlan */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="teachers-plan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'teachers-plan-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Элемент планирования индивидуальных занятий
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'plan_year')->dropDownList(\artsoft\helpers\ArtHelper::getStudyYearsList(),
                                [
                                    'disabled' => $model->plan_year ? true : false,
                                    'options' => [\artsoft\helpers\ArtHelper::getStudyYearDefault() => ['Selected' => $model->isNewRecord ? true : false]
                                    ]
                                ]);
                            ?>
                            <?= $form->field($model, 'teachers_id')->dropDownList(RefBook::find('teachers_fio')->getList(), [
                                'disabled' => true,
                            ]);
                            ?>
                            <?= $form->field($model, 'direction_id')->dropDownList(Direction::getDirectionList(), [
                                'prompt' => Yii::t('art/teachers', 'Select Direction...'),
                                'id' => 'direction_id'
                            ]);
                            ?>

                            <?= $form->field($model, "week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList()) ?>
                            <?= $form->field($model, "week_day")->dropDownList(['' => Yii::t('art/guide', 'Select week day...')] + \artsoft\helpers\ArtHelper::getWeekdayList()) ?>
                            <?= $form->field($model, "time_plan_in")->textInput()->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                            <?= $form->field($model, "time_plan_out")->textInput()->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                            <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList()) ?>
                            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
