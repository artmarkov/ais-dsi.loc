<?php

use yii\helpers\Html;
use artsoft\block\models\Block;

/* @var $this yii\web\View */

$this->title = Block::getTitle('main-info');
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
                        <?= Block::getHtml('main-info') ?>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <?= Html::a('Общие правила',
                    ['/site/common-rules'],
                    [
                        'class' => 'btn btn-default btn-md'
                    ]);
                ?>
                <?= Html::a('Политика конфиденциальности',
                    ['/site/privacy-policy'],
                    [
                        'class' => 'btn btn-default btn-md'
                    ]);
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
