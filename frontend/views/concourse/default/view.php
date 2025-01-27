<?php

use artsoft\widgets\ActiveForm;
use common\models\concourse\ConcourseItem;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\question\QuestionAttribute */
/* @var $form artsoft\widgets\ActiveForm */

$readonly = false;
$users_list = artsoft\models\User::getUsersListByCategory(['teachers'], false);
?>

<div class="concourse-item-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => false]
        ],
        'id' => 'concourse-item-form',
        'validateOnBlur' => false,
    ])
    ?>

    <div class="panel">
        <div class="panel-heading">
            Карточка конкурсной работы
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <?= DetailView::widget([
                        'model' => $modelItem,
                        'attributes' => [
                            'name',
                            'description:ntext',
                            [
                                'attribute' => 'authors_list',
                                'value' => function (ConcourseItem $modelItem) use ($users_list) {
                                    $v = [];
                                    foreach ($modelItem->authors_list as $id) {
                                        if (!$id) {
                                            continue;
                                        }
                                        $v[] = $users_list[$id] ?? $id;
                                    }
                                    return implode(', ', $v);
                                },
                                'format' => 'raw',
                            ],
                        ],
                    ]);
                    ?>

                </div>
            </div>
                <div class="panel">
                    <div class="panel-heading">
                        Материалы конкурсной работы
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?= artsoft\fileinput\widgets\FileInput::widget([
                                    'model' => $modelItem,
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
                            </div>
                        </div>
                    </div>
                </div>

                <?= $this->render('@backend/views/concourse/concourse-answers/_form.php', [
                    'model' => $model,
                    'id' => $id,
                    'objectId' => $objectId,
                    'modelsItems' => $modelsItems,
                ]); ?>

        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>
