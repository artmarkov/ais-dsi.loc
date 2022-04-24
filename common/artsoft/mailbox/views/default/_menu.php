<?php

use artsoft\widgets\Nav;
use artsoft\mailbox\models\MailboxInbox;
use artsoft\mailbox\MailboxAssetsBundle;

MailboxAssetsBundle::register($this);
?>

<div class="mailbox-menu">
    <div class="mailbox-nav">
        <?php
        $menuItems = [
            [
                'encode' => false,
                'label' => '<i class="fa fa-inbox" style="margin-right: 5px;"></i>' . Yii::t('art/mailbox', 'Inbox') . MailboxInbox::getLabelNewMail(),
                'url' => ['/mailbox/default/index'],
                'active' => true
            ],
            [
                'encode' => false,
                'label' => '<i class="fa fa-envelope-o" style="margin-right: 5px;"></i>' . Yii::t('art/mailbox', 'Sent'),
                'url' => ['/mailbox/default/index-sent']
            ],
            [
                'encode' => false,
                'label' => '<i class="fa fa-file-text-o" style="margin-right: 5px;"></i>' . Yii::t('art/mailbox', 'Drafts'),
                'url' => ['/mailbox/default/index-draft']
            ],
            [
                'encode' => false,
                'label' => '<i class="fa fa-trash-o" style="margin-right: 5px;"></i>' . Yii::t('art/mailbox', 'Trash'),
                'url' => ['/mailbox/default/index-trash']
            ],
        ];
        echo Nav::widget([
            'id' => 'mailbox',
            'options' => ['class' => 'nav'],
            'items' => $menuItems,
        ]);
        ?>

    </div>
</div>

<?php
$css = <<<CSS

#mailbox .mm-active {
    background-color: #eee;
}
        
CSS;

$this->registerCss($css);


