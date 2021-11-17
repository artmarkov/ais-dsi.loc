<?php

use artsoft\widgets\ActiveForm;
use artsoft\models\User;
use common\models\info\Board;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\info\Board */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="board-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'board-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">

        <div class="panel-heading">
            Карточка объявления
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php if (\artsoft\Art::isBackend() && User::hasPermission('editBoardAuthor')): ?>
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
                    <?php endif; ?>
                    <?= $form->field($model, 'category_id')->radioList(Board::getCategoryListRuleFilter()) ?>
                    <?php
                    $options = ($model->category_id != 10) ? ['options' => ['style' => 'display:none']] : [];
                    ?>

                    <?= $form->field($model, 'recipients_list', $options)->widget(\kartik\select2\Select2::class, [
                        'data' => User::getUsersListByCategory(Board::getCategorySelectListRuleFilter()),
                        'options' => [
//                                    'disabled' => $readonly,
                            'placeholder' => Yii::t('art/info', 'Select Recipients...'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                    <?= $form->field($model, 'importance_id')->dropDownList(Board::getImportanceList(), [
                        'options' => [
                            Board::IMPORTANCE_NORM => ['selected' => true]
                        ]
                    ]) ?>

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'board_date')->widget(DatePicker::class, ['options' => ['value' => date('d.m.Y')]]); ?>

                    <?= $form->field($model, 'delete_date')->widget(DatePicker::class); ?>

                    <?= $form->field($model, 'status')->dropDownList(Board::getStatusList()); ?>
                </div>
            </div>
            <?php if (!$model->isNewRecord) : ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Загруженные материалы
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $model, 'options' => ['multiple' => true], 'disabled' => false]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
<?php
$js = <<<JS
    $('input[name="Board[category_id]"]').click(function(){
       $(this).val() === '10' ? $('.field-board-recipients_list').show() : $('.field-board-recipients_list').hide();
     });
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);
?>
