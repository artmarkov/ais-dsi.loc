<?php
/**
 * @var Tree $node
 */

use artsoft\models\User; ?>
<div class="row">
    <div class="col-sm-12">

        <?= $form->field($node, 'created_by')->dropDownList(User::getUsersList()) ?>

        <?= $form->field($node, 'rules_list_read')->widget(\kartik\select2\Select2::className(), [
            'data' => \common\models\info\FilesCatalog::getRoleList(),
            'showToggleAll' => false,
            'options' => [
                'disabled' => $node->isReadonly(),
                'placeholder' => Yii::t('art/guide', 'Select Rules...'),
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => false,
            ],

        ]);
        ?>
        <?= $form->field($node, 'rules_list_edit')->widget(\kartik\select2\Select2::className(), [
            'data' => \common\models\info\FilesCatalog::getRoleList(),
            'showToggleAll' => false,
            'options' => [
                'disabled' => $node->isReadonly(),
                'placeholder' => Yii::t('art/guide', 'Select Rules...'),
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => false,
            ],

        ]);
        ?>

    </div>
</div>
