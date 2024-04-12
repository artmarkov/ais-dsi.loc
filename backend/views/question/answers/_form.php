<?php

use artsoft\helpers\Html;
use artsoft\widgets\ActiveForm;
use artsoft\helpers\ButtonHelper;
use common\models\question\Question;
use common\models\question\QuestionAttribute;
use common\models\question\QuestionAnswers;
use common\models\question\QuestionUsers;

/* @var $this yii\web\View */
/* @var $modelQuestion */
/* @var $model */
/* @var $readonly */

$options = [];
//echo '<pre>' . print_r($model->getAttributesType(), true) . '</pre>';
?>

<div class="answers-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['readonly' => $readonly]
        ],
        'id' => 'answers-form',
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data'],
    ])
    ?>
    <div class="panel">
        <div class="panel-heading">
            Карточка формы
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= $modelQuestion->name ?>
                </div>
                <div class="panel-body">
                    <?php if ($modelQuestion->description): ?>
                        <?= \yii\bootstrap\Alert::widget([
                            'body' => '<i class="fa fa-info"></i> ' . $modelQuestion->description,
                            'options' => ['class' => 'alert-info'],
                        ]);
                        ?>
                        <?= artsoft\fileinput\widgets\FileInput::widget([
                            'model' => $modelQuestion,
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
                    <?php endif; ?>
                    <?php if ($modelQuestion->users_cat != Question::GROUP_GUEST): ?>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Пользователь
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <?= $form->field($model, 'users_id')->widget(\kartik\select2\Select2::class, [
                                        'data' => \artsoft\models\User::getUsersListByCategory($modelQuestion->getUsersCategory()),
                                        'showToggleAll' => false,
                                        'options' => [
                                            'disabled' => $readonly,
                                            'value' => $model->users_id,
                                            'placeholder' => Yii::t('art', 'Select...'),
                                            'multiple' => false,
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => false,
                                        ],

                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?= Html::activeHiddenInput($model, 'users_id'); ?>
                    <?php endif; ?>
                    <div class="row">
                        <?= $form->field($model, 'read_flag')->dropDownList(QuestionUsers::getReadList(), ['disabled' => true]) ?>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <?= 'Заполните поля формы'; ?>
                        </div>
                        <div class="panel-body">
                            <?php foreach ($model->getModels()->all() as $id => $item): ?>
                                <div class="row col-sm-offset-2">
                                    <?php if ($item->description): ?>
                                        <?= \yii\bootstrap\Alert::widget([
                                            'body' => '<i class="fa fa-info"></i> ' . $item->description,
                                            'options' => ['class' => 'alert-info'],
                                        ]);
                                        ?>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <?php if ($model->getAttributesType()[$item['name']] == QuestionAttribute::TYPE_FILE): ?>
                                        <div class="col-sm-11">
                                            <?= $model->getForm($form, $item, ['readonly' => $readonly]); ?>
                                        </div>
                                        <div class="col-sm-1">
                                            <?php
                                            if (!empty($model[$item['name']])) {
                                                echo Html::a('<img src="' . QuestionAnswers::getFileContent($model[$item['name']]) . '"/>', ['question/default/download', 'id' => $model->getValueId($item['name'])]);
                                            }
                                            ?>
                                        </div>
                                    <?php else: ?>
                                        <?= $model->getForm($form, $item, ['readonly' => $readonly]); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <?= \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info"></i> ' . 'Поля, помеченные * обязательны для заполнения',
                        'options' => ['class' => 'alert-warning'],
                    ]);
                    ?>
                    <?= \artsoft\Art::isFrontend() ? \yii\bootstrap\Alert::widget([
                        'body' => '<i class="fa fa-info"></i> ' . 'Нажимая кнопку "Отправить данные формы", Вы соглашаетесь на обработку персональных данных!',
                        'options' => ['class' => 'alert-info'],
                    ]) : '';
                    ?>
                    <div class="form-group btn-group">
                        <?= ButtonHelper::exitButton(); ?>
                        <?php if (!$readonly): ?>
                            <?= ButtonHelper::saveButton('submitAction', 'saveexit', 'Отправить данные формы'); ?>
                        <?php else: ?>
                            <?= ButtonHelper::updateButton($model); ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

    </div>

    <?php
    $css = <<<CSS
img {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    border: 1px solid #3b5876;
    padding: 1px;
    vertical-align: middle;
}
CSS;
    $this->registerCss($css);
    ?>
