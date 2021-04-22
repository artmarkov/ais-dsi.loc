<?php 
use artsoft\widgets\ActiveForm;
/**
 * @var Tree $node
*/

?>
<div class="row">
    <div class="col-sm-12">

        <?= $form->field($node, 'description')->textarea(['rows' => 3]) ?>
        <?= $form->field($node, 'value_default')->textInput(['maxlength' => true]) ?>

    </div>
</div>
