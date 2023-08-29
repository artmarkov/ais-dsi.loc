<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use yii\helpers\Url;
use artsoft\helpers\RefBook;

/* @var $this yii\web\View */
/* @var $model common\models\teachers\TeachersLoad */
/* @var $studyplanSubjectModel common\models\studyplan\StudyplanSubject */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="teachers-load-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'teachers-load-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Элемент нагрузки преподавателя:
            <?php echo RefBook::find('subject_memo_2')->getValue($model->studyplan_subject_id); ?>
            <?php echo RefBook::find('sect_name_2')->getValue($model->subject_sect_studyplan_id); ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    echo Html::activeHiddenInput($model, 'subject_sect_studyplan_id');
                    echo Html::activeHiddenInput($model, 'studyplan_subject_id');
                    ?>
                    <?= $form->field($model, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\teachers\Teachers::getTeachersAll($model->isNewRecord ? \common\models\teachers\Teachers::STATUS_ACTIVE : null),
                        'options' => [
                            'id' => 'teachers_id',
                            'disabled' => false,
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ]);
                    ?>
                    <?= $form->field($model, 'direction_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => \common\models\teachers\TeachersActivity::getDirectionListForTeachers($model->teachers_id),
                        'options' => [
                            'id' => 'direction_id',
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'depends' => ['teachers_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => Url::to(['/teachers/default/direction'])
                        ],
                    ]);
                    ?>


                    <?= $form->field($model, 'direction_vid_id')->widget(\kartik\depdrop\DepDrop::class, [
                        'data' => \common\models\teachers\TeachersActivity::getDirectionVidListForTeachers($model->teachers_id, $model->direction_id),
                        'options' => [
                            'placeholder' => Yii::t('art', 'Select...'),
                        ],
                        'pluginOptions' => [
                            'depends' => ['teachers_id', 'direction_id'],
                            'placeholder' => Yii::t('art', 'Select...'),
                            'url' => Url::to(['/teachers/default/direction-vid'])
                        ]
                    ]);
                    ?>

                    <?= $form->field($model, 'load_time')->textInput() ?>

                    <?= $form->field($model, 'load_time_consult')->textInput() ?>

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
