<?php

use artsoft\helpers\Html;
use artsoft\helpers\RefBook;
use artsoft\models\User;
use artsoft\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\SubjectCharacteristic */
/* @var $studyplanSubjectModel common\models\studyplan\StudyplanSubject */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="subject-characteristic-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'subject-characteristic-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Элемент характеристики по предметам:
            <?php echo RefBook::find('subject_memo_4')->getValue($studyplanSubjectModel->id); ?>
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    echo Html::activeHiddenInput($model, 'studyplan_subject_id');
                    ?>

                    <?= $form->field($model, 'teachers_id')->dropDownList(RefBook::find('teachers_fio')->getList(), [
                        'disabled' => false,
                    ]);
                    ?>
                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

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
