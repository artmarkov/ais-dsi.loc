<?php 
/**
 * @var Tree $node
*/

?>
<div class="row">
    <div class="col-sm-12">

        <?= $form->field($node, 'description')->textarea(['rows' => 3]) ?>
        <?= $form->field($node, 'category_sell')->radioList(\common\models\guidesys\GuidePlanTree::getCategoryList())->label(false) ?>
        <?= $form->field($node, 'protocol_flag')->checkbox() ?>
        <?= $form->field($node, 'perform_flag')->checkbox() ?>
        <?= $form->field($node, 'preparing_flag')->checkbox() ?>
        <?= $form->field($node, 'description_flag')->checkbox() ?>
        <?= $form->field($node, 'afisha_flag')->checkbox() ?>
        <?= $form->field($node, 'rider_flag')->checkbox() ?>
        <?= $form->field($node, 'bars_flag')->checkbox() ?>
        <?= $form->field($node, 'efficiency_flag')->checkbox() ?>
        <?= $form->field($node, 'schedule_flag')->checkbox() ?>
        <?= $form->field($node, 'partners_flag')->checkbox() ?>

    </div>
</div>
