<div class="panel panel-default">
    <div class="panel-heading">
        <?= $model->name ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $model->description ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= artsoft\fileinput\widgets\FileInput::widget([
                                    'model' => $model,
                                    'disabled' => false,
                                    'pluginOptions' => [
                                        'deleteUrl' => null,
                                        'showCaption' => false,
                                        'showBrowse' => false,
                                        'showUpload' => false,
                                        'fileActionSettings' => [
                                            'showDrag' => false,
                                            'showRemove' => false,
                                        ],
                                    ],
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// TODO: костыль, не отключилась кнопка через 'showRemove' => false
$css = <<<CSS
.kv-file-remove {
    display: none; 
}

CSS;

$this->registerCss($css);
?>