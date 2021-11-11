<?php

use artsoft\widgets\ActiveForm;
use yii\helpers\Html;
use artsoft\block\models\Block;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */

$this->title = Block::getTitle('support');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if (!Yii::$app->user->isGuest): ?>
    <div class="support">
        <div class="panel panel">
            <div class="panel-body">
                <div class="col-sm-12">
                    <?= Block::getHtml('support', [
                        'general_title' => Yii::$app->settings->get('general.title', 'Art Site'),
                        'general_email' => Yii::$app->settings->get('general.email'),
                        'general_phone' => Yii::$app->settings->get('general.phone')
                    ]) ?>

                    <?php $form = ActiveForm::begin(['id' => 'support-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?= $this->title ?>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?= $form->field($model, 'name') ?>
                                    <?= $form->field($model, 'email') ?>
                                    <?= $form->field($model, 'phone')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                                    <?= $form->field($model, 'phone_optional')->widget(MaskedInput::class, ['mask' => Yii::$app->settings->get('reading.phone_mask')])->textInput() ?>
                                    <?= $form->field($model, 'subject') ?>
                                    <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
                                    <?= $form->field($model, 'file')->fileInput() ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div class="form-group">
                                <?= Html::a('На главную', Yii::$app->urlManager->hostInfo, ['class' => 'btn btn-default btn-md']); ?>
                                <?= Html::submitButton('<i class="fa fa-paper-plane-o" style="margin-right: 5px;"></i>' . 'Отправить', ['class' => 'btn btn-primary btn-md', 'name' => 'support-button']) ?>
                            </div>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
