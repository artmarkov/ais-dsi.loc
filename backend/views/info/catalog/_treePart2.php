<?php
/**
 * @var Tree $node
 */

?>
<div class="row">
    <div class="col-sm-12">

        <?= $form->field($node, 'description')->textarea(['rows' => 6, 'disabled' => $node->isReadonly()]) ?>

    </div>
</div>
<?php if (!$node->isNewRecord) : ?>
    <div class="row">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $node, 'options' => ['multiple' => true], 'disabled' => $node->isReadonly()]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
