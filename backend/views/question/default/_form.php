<?php

use artsoft\models\User;
use artsoft\widgets\ActiveForm;
use common\models\info\Board;
use common\models\question\Question;
use artsoft\helpers\Html;
use kartik\date\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\question\Question */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="question-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'question-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    <?= $form->field($model->loadDefaultValues(), 'author_id')->widget(\kartik\select2\Select2::class, [
                        'data' => User::getUsersListByCategory(['teachers', 'employees']),
                        'showToggleAll' => false,
                        'options' => [
//                            'disabled' => $readonly,
                            'value' => $model->isNewRecord ? Yii::$app->user->id : $model->author_id,
                            'placeholder' => Yii::t('art/guide', 'Select Authors...'),
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'minimumInputLength' => 3,
                        ],

                    ]);
                    ?>
                </div>

                <?= $form->field($model, 'name')->textInput() ?>

                <?= $form->field($model, 'category_id')->radioList(Question::getCategoryList()) ?>

                <?= $form->field($model, 'users_cat')->widget(Select2::className(), [
                    'data' => Question::getGroupList(),
                    'options' => [
                        // 'disabled' => $readonly,
                        'placeholder' => Yii::t('art/guide', 'Select...'),
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>

                <?= $form->field($model, 'vid_id')->radioList(Question::getVidList()) ?>

                <?= $form->field($model, 'division_list')->widget(Select2::className(), [
                    'data' => \common\models\own\Division::getDivisionList(),
                    'options' => [
                        // 'disabled' => $readonly,
                        'placeholder' => Yii::t('art/guide', 'Select Division...'),
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label(Yii::t('art/guide', 'Division'));
                ?>

                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'timestamp_in')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', /*'disabled' => $readonly*/]); ?>

                <?= $form->field($model, 'timestamp_out')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', /*'disabled' => $readonly*/]); ?>

                <?= $form->field($model, 'status')->dropDownList(Question::getStatusList(), [/*'disabled' => $readonly*/]) ?>

                <?= $form->field($model, 'email_flag')->checkbox([/*'disabled' => $readonly*/]) ?>

                <?= $form->field($model, 'email_author_flag')->checkbox([/*'disabled' => $readonly*/]) ?>
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
