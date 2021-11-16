<div class="panel panel-default">
    <div class="panel-heading">
        <?= $model->name ?>
    </div>
    <div class="panel-body">
        <?php if ($model->description): ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    Основная информация
                </div>
                <div class="panel-body">
                    <div class="col-sm-12">
                        <?= $model->description ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($model->youtube_code): ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    Видеоматериалы с YouTube
                </div>
                <div class="panel-body">
                    <div class="col-sm-12">
                        <?php foreach (explode(',', $model->youtube_code) as $code): ?>
                            <?= \WolfpackIT\youtube\widgets\YouTube::widget([
                                'id' => 'yt-video-player',
                                'videoId' => $code,
                                'enableJsApi' => false,

                            ]); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($model->getFilesCount() != 0): ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    Внутренние ресурсы
                </div>
                <div class="panel-body">
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
        <?php endif; ?>
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