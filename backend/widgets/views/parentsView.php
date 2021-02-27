<?php

use artsoft\widgets\ActiveForm;
use common\models\student\Student;
use artsoft\helpers\Html;
use kartik\select2\Select2;

?>
<?php $form = ActiveForm::begin(); ?>

<div class="parens-view-widget">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">

                            <?php //$categories = \yii\helpers\ArrayHelper::merge(\common\models\user\UserCommon::getUserParentList(), ['0' => Yii::t('art/user', '---Create New Parent---')]) ?>

                            <?=
                            $form->field($model, 'user_slave_id')->widget(Select2::classname(), [

                                'data' => \common\models\user\UserCommon::getUserParentList($model->user_id),
                                'theme' => Select2::THEME_KRAJEE,
                                'options' => ['placeholder' => Yii::t('art/user', 'Create New Parent...')],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                'addon' => [
                                    'append' => [
                                        'content' => Html::a(Yii::t('art', 'Create'), ['#'], [
                                            'class' => 'btn btn-primary add-to-family',
                                            'data-id' => $model->user_id,
                                        ]),
                                        'asButton' => true,
                                    ],
                                ],
                            ])->label(Yii::t('art/user', 'Parents'));
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">

                            <?php $data = Student::getFamilyList($model->user_id); ?>
<?php if (!empty($data)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>№</th>
                                                <th><?= Yii::t('art', 'Full Name'); ?></th>
                                                <th><?= Yii::t('art/user', 'Family Relation'); ?></th>
                                                <th><?= Yii::t('art', 'Phone'); ?></th>
                                                <th><?= Yii::t('art', 'E-mail'); ?></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
    <?php foreach ($data as $id => $item): ?>
                                                <tr>
                                                    <td><?= ++$id ?></td>
                                                    <td><?= $item['parent'] ?></td>
                                                    <td><?= $item['relation'] ?></td>
                                                    <td><?= $item['phone'] ?></td>
                                                    <td><?= $item['email'] ?></td>

                                                    <td><?=
                                                        Html::a('<span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>', ['#'], [
                                                            'class' => 'remove-family',
                                                            'data-id' => $item['id'],
                                                        ]);
                                                        ?></td>
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
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS

function showParent(parent) {
    $('#parents-modal .modal-body').html(parent);
    $('#parents-modal').modal();
}

$('.add-to-family').on('click', function (e) {

    e.preventDefault();
    
    var id = $(this).data('id'),
        user_slave_id = $('#student-user_slave_id').val();

    $.ajax({
        url: '/admin/parent/default/init-family',
        data: {id: id, user_slave_id: user_slave_id},
        type: 'GET',
        success: function (res) {
            if (!res)  alert('Error!');
          //  console.log(res);
            showParent(res);
        },
        error: function () {
            alert('Error!');
        }
    });
});

$('.remove-family').on('click', function (e) {

    e.preventDefault();
    
    var id = $(this).data('id');

    $.ajax({
        url: '/admin/student/default/remove',
        data: {id: id},
        type: 'GET'
    });
});

JS;

$this->registerJs($js);
?>