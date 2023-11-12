<?php


/* @var $this yii\web\View */
?>

<div class="panel panel-default">
    <div class="panel-heading">Доска объявлений</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 30%;">Автор/Дата объявления</th>
                        <th class="text-center">Тема/Содержание</th>
                    </tr>
                    </thead>
                    <tbody class="container-items">
                    <?php foreach ($models as $index => $model): ?>
                        <tr class="warning">
                            <td style="font-weight: bold">
                                <?= $model->author->userCommon ? $model->author->userCommon->fullName : $model->author_id; ?>
                            </td>
                            <td style="font-weight: bold">
                                <?= $model->importance_id == \common\models\info\Board::IMPORTANCE_HI ? "<i style='color: red' class='fa fa-exclamation'></i>" : '' ?>
                                <?= $model->title; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $model->board_date; ?>
                            </td>
                            <td>
                                <?= $model->description; ?>

                                <?= artsoft\fileinput\widgets\FileInput::widget([
                                    'model' => $model,
                                    'pluginOptions' => [
                                        'deleteUrl' => false,
                                        'showRemove' => false,
                                        'showCaption' => false,
                                        'showBrowse' => false,
                                        'showUpload' => false,
                                        'dropZoneEnabled' => false,
                                        'showCancel' => false,
                                        'initialPreviewShowDelete' => false,
                                        'fileActionSettings' => [
                                            'showDrag' => false,
                                            'showRotate' => false,
                                        ],
                                    ],
                                ]);
                                ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
