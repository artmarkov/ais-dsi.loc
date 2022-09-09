<?php 
/**
 * @var Tree $node
*/

?>
<div class="row">
    <div class="col-sm-12">

        <?= $form->field($node, 'description')->textarea(['rows' => 3]) ?>
        <?= $form->field($node, 'bonus_vid_id')->radioList(\common\models\efficiency\EfficiencyTree::getBobusVidList()) ?>
        <?= $form->field($node, 'value_default')->textInput(['maxlength' => true])->hint('') ?>
        <?= $form->field($node, 'class')->textInput(['maxlength' => true]) ?>

    </div>
</div>
