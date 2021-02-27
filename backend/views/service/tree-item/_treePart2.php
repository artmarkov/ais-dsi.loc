<?php 
use artsoft\widgets\ActiveForm;
?>
<div class="row">
    <div class="col-sm-8">

        <?= $form->field($node, 'content_type')->dropDownList($node->getTypeList()) ?>

    </div>
</div>
