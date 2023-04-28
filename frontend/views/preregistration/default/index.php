<?php

use yii\helpers\Html;
use artsoft\block\models\Block;

/* @var $this yii\web\View */

$this->title = 'Запись на обучение';
?>
<div class="site-index">
    <?php if (Yii::$app->user->isGuest): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $this->title?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">

                    </div>
                </div>
            </div>
            <div class="panel-footer">

            </div>
        </div>
    <?php endif; ?>
</div>
