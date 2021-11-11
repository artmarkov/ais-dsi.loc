<?php
/**
 * @var Tree $node
 */

?>
<div class="row">
    <div class="col-sm-12">

        <?= $form->field($node, 'description')->textarea(['rows' => 3]) ?>

    </div>
</div>
<?php if (!$node->isNewRecord) : ?>
    <div class="row">
        <div class="panel panel-info">
            <div class="panel-heading">
                Материалы
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= artsoft\fileinput\widgets\FileInput::widget(['model' => $node, 'options' => ['multiple' => true], /*'disabled' => $readonly*/]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
