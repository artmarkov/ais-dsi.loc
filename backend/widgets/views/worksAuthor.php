<?php

use artsoft\widgets\ActiveForm;
use artsoft\helpers\Html;
use kartik\select2\Select2;

?>
<?php $form = ActiveForm::begin(); ?>
    <div class="works-author-widget">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <?= $form->field($model, 'author_id')->widget(Select2::classname(), [

                        'data' => common\models\user\UserCommon::getWorkAuthorTeachersList(),
                        'theme' => Select2::THEME_KRAJEE,
                        'options' => ['placeholder' => Yii::t('art/user', 'Select teacher...')],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                        'addon' => [
                            'append' => [
                                'content' => Html::a(Yii::t('art', 'Add'), ['#'], [
                                    'class' => 'btn btn-primary add-to-works-author',
                                    'data-id' => $model->id,
                                ]),
                                'asButton' => true,
                            ],
                        ],
                    ])->label(Yii::t('art/creative', 'Works authors'));
                    ?>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <label class="control-label"></label>
                    </div>
                    <div class="col-sm-9">
                        <?php $data = \common\models\creative\CreativeWorksAuthor::getWorksAuthorList($model->id); ?>
                        <?php if (!empty($data)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= Yii::t('art', 'Full Name'); ?></th>
                                        <th><?= Yii::t('art/creative', 'Weight'); ?></th>
                                        <th><?= Yii::t('art/creative', 'Time weight'); ?></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($data as $id => $item): ?>
                                        <tr>
                                            <td><?= ++$id ?></td>
                                            <td><?= $item['author'] ?></td>
                                            <td><?= $item['weight'] ?></td>
                                            <td><?= Yii::$app->formatter->asDate($item['timestamp'], 'MMM Y') ?></td>
                                            <td><?= Html::a('<span class="glyphicon glyphicon-pencil text-color-default" aria-hidden="true"></span>', ['#'], [
                                                    'class' => 'update-author',
                                                    'data-id' => $item['id'],
                                                ]);
                                                ?>
                                                <?= Html::a('<span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>', ['#'], [
                                                    'class' => 'remove-author',
                                                    'data-id' => $item['id'],
                                                ]);
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS

function showAuthor(author) {
    $('#works-author-modal .modal-body').html(author);
    $('#works-author-modal').modal();
}

$('.add-to-works-author').on('click', function (e) {

    e.preventDefault();
    var id = $(this).data('id'),
        author_id = $('#creativeworks-author_id').val();
     // console.log(author_id);
     // console.log(id);

    $.ajax({
        url: '/admin/creative/works-author/init-author',
        data: {id: id, author_id: author_id},
        type: 'GET',
        success (res) {
            if (!res)  alert('Please select teacher...');
           // console.log(res);
           else showAuthor(res);
        },
        error () {
            alert('Script Error!');
        }
    });
});

$('.update-author').on('click', function (e) {

    e.preventDefault();

    var id = $(this).data('id');

    $.ajax({
        url: '/admin/creative/works-author/update-author',
        data: {id: id},
        type: 'GET',
        success (res) {
            if (!res)  alert('Error!');
           // console.log(res);
           else showAuthor(res);
        },
        error () {
            alert('Error!');
        }
    });
});

$('.remove-author').on('click', function (e) {

    e.preventDefault();
    
    var id = $(this).data('id');

    $.ajax({
        url: '/admin/creative/works-author/remove',
        data: {id: id},
        type: 'GET'
    });
});

JS;

$this->registerJs($js);
?>