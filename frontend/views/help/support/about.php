<?php

use yii\helpers\Html;
use artsoft\block\models\Block;

/* @var $this yii\web\View */

$this->title = Block::getTitle('about');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="privacy-policy">
    <?php if (!Yii::$app->user->isGuest): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $this->title ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= Block::getHtml('about', ['general_title' => Yii::$app->settings->get('general.title', 'Art Site')]) ?>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <?= Html::a('На главную',
                    Yii::$app->urlManager->hostInfo,
                    [
                        'class' => 'btn btn-default btn-md'
                    ]);
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>