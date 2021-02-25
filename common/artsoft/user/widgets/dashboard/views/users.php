<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$defaultAvatar = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAA21BMVEX2+PnS0tLm6Onp6+zz9vfW1tbu8fLy9PbT09PW19fQ0NDq7O3s7vDn6era2tvV1tbw8vPY2dn1+Pna3NzT1NTl5+jr7e7y9fb09vjt8PHa29vp7O3W1tfk5ufo6uvx9PXc3d3p7OzU1NTw8/Tq7e3v8vPe4ODR0dHd3t/U1dXS09Pm6env8vLZ2trd3+Di5OXW19jd3d7f4eHY2trg4uPk5ebU1NXa2trs7/D09/jk5+jy9PXz9vbz9vjh4uPk5uji5OTu8vLo6evr7u/t7/H19/je4OHx8/Tw8vRivI1nAAABWElEQVR4Xt3U1Y7rMBCAYU+YOWVmWmbmPef9n2hVrRQ3cTSe6/2v+8Wxpw77myXT7SrQw2C1nSYUYF5C0fk/OWjrcNDEk4FoA+XaOLgbQaVUQ4EKQj4KFiI4w8AwFMEEOykDeKRtN+pAAwHdOqDKV6ADrw7MEBBlIDZHQCsWgY2O+lgEI4bVF8EVCjx8DGKtoyo41RiaUgV9hqdVlug1OSD9OwaMRzmoE0Yo3/ANJDLQdE3Gbju/IPhgrDkzkS9YpFzDxf4Hu7HyMjb2D7iBTNnVr9Pqpo4DALpfnPzTwAYAxwlUTQQRH5l9bzw8asO5EvPxGcLLh5Xz70EpqzqPZ8BbvpbB0AJZbyXwDtIU8eLgjRLG09ZykB1uuw2EPjlIVArwv/iQ+wSgf/NRmAsCsNL/BTA6BLDsGAXwYiBkuwVwdQoI+XX11xRg5QXILRSIF9y1KUBX9+AH/N4XLH6KeQQAAAAASUVORK5CYII=';
?>

<div class="panel panel-default dw-widget">
    <div class="panel-heading"><?= Yii::t('art/user', 'Users') ?></div>
    <div class="panel-body">

        <?php if (count($recent)): ?>
            <div class="clearfix">
                <?php foreach ($recent as $item) : ?>
                    <div class="clearfix dw-user">
                        <div class="avatar">
                            <?php $avatar = ($userAvatar = $item->getAvatar()) ? $userAvatar : $defaultAvatar ?>
                            <img src="<?= $avatar ?>"/>
                        </div>

                        <div class="dw-user-info">
                            <div>
                                <b><?= Yii::t('art', 'Login') ?>:</b>
                                <a class="author"><?= $item->username ?></a>
                            </div>
                            <div>
                                <b><?= Yii::t('art', 'E-mail') ?>:</b>
                                <span><?= $item->email ?></span>
                            </div>
                            <div>
                                <b><?= Yii::t('art', 'Data') ?>:</b>
                                <span><?= "{$item->createdDate} {$item->createdTime}" ?></span>
                            </div>
                            <div>
                                <b><?= Yii::t('art', 'Registration IP') ?>:</b>
                                <span><?= $item->registration_ip ?></span>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>

            <div class="dw-quick-links">
                <?php $list = [] ?>
                <?php foreach ($users as $item) : ?>
                    <?php $list[] = Html::a("<b>{$item['count']}</b> {$item['label']}", $item['url']); ?>
                <?php endforeach; ?>
                <?= implode(' | ', $list) ?>
            </div>

        <?php else: ?>
            <h4><em><?= Yii::t('art/user', 'No users found.') ?></em></h4>
        <?php endif; ?>

    </div>
</div>

<?php
$css = <<<CSS
.dw-widget{
    position:relative;
    padding-bottom:15px;
}
.dw-user {
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
    margin-bottom: 5px;
}
.dw-user-info {
    padding-left: 60px;
}
.dw-quick-links{
    position: absolute;
    bottom:10px;
    left:0;
    right:0;
    text-align: center;
}
.avatar {
    float: left;
    margin-right: 10px;
    margin-top: 3px;
}

CSS;

$this->registerCss($css);
?>