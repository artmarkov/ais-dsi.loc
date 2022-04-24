<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model artsoft\mailbox\models\Mailbox */

$this->title = Yii::t('art/mailbox', 'Compose');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/mailbox', 'Inbox'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="mailbox-update">
   <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?=  Html::encode($this->title) ?></h3>            
        </div>
   </div>
   <div class="row">
        <div class="col-md-3">
            <?= Html::a(Yii::t('art/mailbox', 'Back to Inbox'), ['/mailbox/default/index'], ['class' => 'btn btn-primary btn-block margin-bottom']) ?>
         
            <div class="panel panel-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('art/mailbox', 'Folders'); ?></h3>
            </div>
                
                <div class="box-body no-padding">                   

                    <?= $this->render('_menu') ?>

                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $this->render('_form', compact('model')) ?>
                </div>
            </div>
        </div>
    </div>
</div>