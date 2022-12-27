<?php

use artsoft\helpers\RefBook;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use common\models\user\UserCommon;
use kartik\date\DatePicker;
use common\models\efficiency\EfficiencyTree;

/* @var $this yii\web\View */
/* @var $form artsoft\widgets\ActiveForm */
?>

<div class="teachers-efficiency-form">
    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'teachers-efficiency-form',
        'validateOnBlur' => false,
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            <?php if (!$model->isNewRecord): ?>
                <span class="pull-right"> <?= \artsoft\helpers\ButtonHelper::historyButton(); ?></span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <?php if ($data = $model->getDependence()): ?>
                <div class="panel">
                    <div class="panel-heading">
                        <?= $data['title'] ?>
                    </div>
                    <div class="panel-body">
                        <?= \yii\widgets\DetailView::widget([
                            'model' => $data['model'],
                            'attributes' => $data['attributes'],
                        ]) ?>
                    </div>
                    <div class="panel-footer">
                        <?= Html::a('<i class="fa fa-arrow-up" aria-hidden="true"></i> Открыть в новом окне',
                            $data['link'],
                            [
                                'target' => '_blank',
                                'class' => 'btn btn-default',
                            ]); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="panel">
                <div class="panel-heading">
                </div>
                <div class="panel-body">
                    <?= $form->field($model, 'efficiency_id')->widget(\kartik\tree\TreeViewInput::class, [
                        'id' => 'efficiency_tree',
                        'options' => [
                            'disabled' => isset($model->class) ? true : $readonly,
                        ],
                        'query' => $model->isNewRecord ? EfficiencyTree::find()->where(['=', 'class', 'TeachersEfficiency'])->addOrderBy('root, lft') : EfficiencyTree::find()->addOrderBy('root, lft'),
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
                        'data' => RefBook::find('teachers_fio', $model->isNewRecord ? UserCommon::STATUS_ACTIVE : '')->getList(),
                        'options' => [
                            'disabled' => isset($model->class) ? true : $readonly,
                            'placeholder' => Yii::t('art/teachers', 'Select Teacher...'),
                            'multiple' => $model->isNewRecord ? true : false,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(Yii::t('art/teachers', 'Teachers'));
                    ?>

                    <?= $form->field($model, 'bonus_vid_id')->radioList(\common\models\efficiency\EfficiencyTree::getBobusVidList(), ['itemOptions' => ['disabled' => $readonly]]) ?>

                    <?= $form->field($model, 'bonus')->textInput(['maxlength' => true, 'readonly' => $readonly]) ?>

                    <?= $form->field($model, 'date_in')->widget(DatePicker::class)->textInput(['autocomplete' => 'off', 'disabled' => $readonly]); ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group btn-group">
                <?= $model->isNewRecord ? \artsoft\helpers\ButtonHelper::saveButton('submitAction', 'saveexit', 'Save & Exit', 'btn-md') :
                    (!$readonly ? \artsoft\helpers\ButtonHelper::submitButtons($model) : \artsoft\helpers\ButtonHelper::viewButtons($model));

                ?>
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
                 let  p = jQuery.parseJSON(bonus);
                    document.getElementsByName('TeachersEfficiency[bonus_vid_id]')[p.id].checked = true;
                    document.getElementById('teachersefficiency-bonus').value = p.value;
            },
            error: function () {
                alert('Error!!!');
            }
        });

});
JS;
$this->registerJs($script, \yii\web\View::POS_READY);
?>
