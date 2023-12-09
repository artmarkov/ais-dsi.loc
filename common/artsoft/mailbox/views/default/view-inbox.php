<?php

use artsoft\helpers\Html;
use artsoft\mailbox\models\MailboxInbox;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model artsoft\mailbox\models\MailboxInbox */

$this->title = Yii::t('art/mailbox', 'Read mail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/mailbox', 'Mailboxs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="mailbox-inbox-view">
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <?= Html::a(Yii::t('art/mailbox', 'Compose'), ['/mailbox/default/compose'], ['class' => 'btn btn-primary btn-block margin-bottom']) ?>

                    <div class="panel panel-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?= Yii::t('art/mailbox', 'Folders'); ?></h3>
                        </div>

                        <div class="box-body no-padding">

                            <?= $this->render('_menu', compact('model')) ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="panel panel-default">
                        <div class="box-header with-border">

                            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

                            <div class="box-tools pull-right">
                                <?= artsoft\mailbox\widgets\PagerSelector::widget([
                                    'prev_id' => MailboxInbox::getPrevMail($model->id),
                                    'next_id' => MailboxInbox::getNextMail($model->id),
                                    'path' => '/mailbox/default/view-inbox',
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="box-body no-padding">
                            <div class="mailbox-read-info">
                                <h3><?= $model->mailbox->title; ?></h3>
                                <h5><?= Yii::t('art/mailbox', 'From:') . ' ' . $model->mailbox->senderName; ?>
                                    <span class="mailbox-read-time pull-right"><?= $model->mailbox->createdDateTime; ?></span>
                                </h5>
                            </div>
                            <div class="mailbox-controls with-border text-center">
                                <div class="btn-group">

                                    <?= Html::a('<i class="fa fa-trash-o"></i>', ['/mailbox/default/trash', 'id' => $model->id], ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'data-container' => 'body', 'title' => '', 'data-original-title' => Yii::t('art/mailbox', 'Move to Trash')]) ?>
                                    <?= Html::a('<i class="fa fa-reply"></i>', ['/mailbox/default/reply', 'id' => $model->mailbox->id], ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'data-container' => 'body', 'title' => '', 'data-original-title' => Yii::t('art/mailbox', 'Reply')]) ?>
                                    <?= Html::a('<i class="fa fa-share"></i>', ['/mailbox/default/forward', 'id' => $model->mailbox->id], ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'data-container' => 'body', 'title' => '', 'data-original-title' => Yii::t('art/mailbox', 'Forward')]) ?>

                                </div>
                            </div>


                            <div id="print_body" class="panel-body">
                                <div class="mailbox-read-message">

                                    <?= $model->mailbox->content; ?>

                                </div>
                            </div>

                            <div class="panel-body">
                                <?= artsoft\fileinput\widgets\FileInput::widget([
                                    'model' => $model->mailbox,
                                    'pluginOptions' => [
                                        'showCaption' => false,
                                        'showBrowse' => false,
                                        'showUpload' => false,
                                        'dropZoneEnabled' => false,
                                        'fileActionSettings' => [
                                            'showDrag' => false,
                                            'showRemove' => false,
                                        ],
                                    ],
                                ]);
                                ?>

                            </div>
                        </div>
                        <div class="box-footer">

                            <div class="pull-right">

                                <?= Html::a('<i class="fa fa-reply" style="margin-right: 5px;"></i>' . Yii::t('art/mailbox', 'Reply'), ['/mailbox/default/reply', 'id' => $model->mailbox->id], ['class' => 'btn btn-default']) ?>
                                <?= Html::a('<i class="fa fa-share" style="margin-right: 5px;"></i>' . Yii::t('art/mailbox', 'Forward'), ['/mailbox/default/forward', 'id' => $model->mailbox->id], ['class' => 'btn btn-default']) ?>

                            </div>

                            <?= Html::a('<i class="fa fa-trash-o" style="margin-right: 5px;"></i>' . Yii::t('art/mailbox', 'Move to Trash'), ['/mailbox/default/trash', 'id' => $model->id], ['class' => 'btn btn-default']) ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
