<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="teachers-efficiency-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'teachers-efficiency-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
            <?php if (!$model->isNewRecord):?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton($model, ['/efficiency/default/history', 'id' => $model->id]); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <?= $form->field($model, 'efficiency_id')->widget(\kartik\tree\TreeViewInput::class, [
                    'options' => [
                        'id' => 'efficiency_tree',
                    ],
                    'id' => 'treeid',
                    'query' => \common\models\efficiency\EfficiencyTree::find()->addOrderBy('root, lft'),
                    'dropdownConfig' => [
                        'input' => ['placeholder' => 'Выберите показатель эффективности...'],
                    ],
                    'fontAwesome' => false,
                    'multiple' => false,
                    'rootOptions' => [
                        'label' => '',
                        'class' => 'text-default'
                    ],
                    'childNodeIconOptions' => ['class' => ''],
                    'defaultParentNodeIcon' => '',
                    'defaultParentNodeOpenIcon' => '',
                    'defaultChildNodeIcon' => '',
                    'childNodeIconOptions' => ['class' => ''],
                    'parentNodeIconOptions' => ['class' => ''],
                ]);
                ?>
                <?= $form->field($model, 'teachers_id')->widget(\kartik\select2\Select2::class, [
                    'data' => \common\models\teachers\Teachers::getTeachersList(),
                    'options' => [
                        // 'disabled' => $readonly,
                        'placeholder' => Yii::t('art/teachers', 'Select Teacher...'),
                        // 'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label(Yii::t('art/teachers', 'Teachers'));
                ?>

                <?= $form->field($model, 'bonus')->textInput(['maxlength' => true, 'readonly' => !Yii::$app->user->isSuperadmin]) ?>

                <?= $form->field($model, 'date_in')->widget(DatePicker::class, [
                    'type' => DatePicker::TYPE_INPUT,
                    'options' => ['placeholder' => ''],
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'dd.MM.yyyy',
                        'minViewMode' => 1,
                        'maxViewMode' => 2,
                        'autoclose' => true,
                    ]
                ])->textInput(['autocomplete' => 'off']); ?>
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


<?php
$script = <<< JS
$("#efficiency_tree").on('treeview:change', function(event, key) {
   // console.log(key);
    $.ajax({
            url: '/admin/efficiency/default/select',
            type: 'POST',
            data: {
                id: key
            },
            success: function (bonus) {
             document.getElementById('teachersefficiency-bonus').value = bonus;
            },
            error: function () {
                alert('Error!!!');
            }
        });

});
JS;
$this->registerJs($script, \yii\web\View::POS_READY);
?>
