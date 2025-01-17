<?php

use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use common\models\question\QuestionAttribute;
use artsoft\helpers\Html;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model common\models\question\QuestionAttribute */
/* @var $form artsoft\widgets\ActiveForm */

$readonly = false;
?>

<div class="concourse-item-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => false]
        ],
        'id' => 'concourse-item-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка конкурсной работы
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    // necessary for update action.
                    echo Html::activeHiddenInput($model, 'concourse_id');
                    ?>
                    <?= $form->field($model, 'name')->textInput() ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'disabled' => $readonly])->hint('Введите описание работы.') ?>

                    <?= $form->field($model, 'authors_list')->widget(Select2::className(), [
                        'data' => User::getUsersListByCategory(['teachers']),
                        'options' => [
                            'disabled' => $readonly,
                            'placeholder' => Yii::t('art', 'Select...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->hint('Укажите авторов работы.');
                    ?>
                </div>
            </div>
            <?php if (!$model->isNewRecord) : ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Материалы конкурсной работы
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'disabled' => false]) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?= $this->render('@backend/views/concourse/concourse-answers/index', compact(['data'])); ?>

            <?php endif; ?>
        </div>
    </div>
    <div class="panel-footer">
        <div class="form-group btn-group">
            <?= \artsoft\helpers\ButtonHelper::submitButtons($model); ?>
        </div>
        <?= \artsoft\widgets\InfoModel::widget(['model' => $model]); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>
