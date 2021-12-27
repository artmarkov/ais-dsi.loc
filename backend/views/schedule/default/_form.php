<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use common\models\guidejob\Direction;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSectSchedule */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="subject-sect-schedule-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'subject-sect-schedule-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Элемент расписания занятий
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php echo RefBook::find('subject_memo_2')->getValue($model->studyplan_subject_id);?>
                    <?php echo RefBook::find('sect_name_1')->getValue($model->subject_sect_studyplan_id);?>

                    <?= Html::activeHiddenInput($model, 'subject_sect_studyplan_id') ?>
                    <?= Html::activeHiddenInput($model, 'studyplan_subject_id') ?>

                    <?= $form->field($model, "direction_id")->widget(\kartik\select2\Select2::class, [
                        'data' => Direction::getDirectionList(),
                        'options' => [
                            'id' => 'direction_id',
                            // 'disabled' => $readonly,
                            'placeholder' => Yii::t('art/teachers', 'Select Direction...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])->label(Yii::t('art/teachers', 'Name Direction'));
                    ?>

                    <?= $form->field($model, "teachers_id")->widget(DepDrop::class, [
                        'data' => \common\models\teachers\Teachers::getTeachersList($model->direction_id),
                        'options' => ['prompt' => Yii::t('art/teachers', 'Select Teacher...'),
                            //     'disabled' => $readonly,
                        ],
                        'pluginOptions' => [
                            'depends' => ['direction_id'],
                            'placeholder' => Yii::t('art/teachers', 'Select Teacher...'),
                            'url' => Url::to(['/teachers/default/teachers'])
                        ]
                    ])->label(Yii::t('art/teachers', 'Teacher'));
                    ?>

                    <?= $form->field($model, "week_num")->dropDownList(['' => Yii::t('art/guide', 'Select week num...')] + \artsoft\helpers\ArtHelper::getWeekList()) ?>
                    <?= $form->field($model, "week_day")->dropDownList(['' => Yii::t('art/guide', 'Select week day...')] + \artsoft\helpers\ArtHelper::getWeekdayList()) ?>
                    <?= $form->field($model, "time_in")->textInput()->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "time_out")->textInput()->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.time_mask')]) ?>
                    <?= $form->field($model, "auditory_id")->dropDownList(['' => Yii::t('art/guide', 'Select auditory...')] + RefBook::find('auditory_memo_1')->getList()) ?>
                    <?= $form->field($model, "description")->textarea() ?>

                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= \artsoft\helpers\ButtonHelper::submitButtons($model) ?>
            </div>
            <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
