<?php

use artsoft\auth\assets\AvatarAsset;
use frontend\assets\AppAsset;
use frontend\assets\ThemeAsset;
use artsoft\assets\MetisMenuAsset;
use artsoft\assets\ArtAsset;
use artsoft\models\Menu;
use artsoft\widgets\LanguageSelector;
use artsoft\widgets\Nav as Navigation;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
ThemeAsset::register($this);
$assetBundle = ArtAsset::register($this);
MetisMenuAsset::register($this);
AvatarAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">

    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

        <?php
        $logo = $assetBundle->baseUrl . '/images/art-logo-inverse.png';
        NavBar::begin([
            'brandLabel' => Html::img($logo, ['class' => 'art-logo', 'alt' => 'ArtCMS']) . '<b>' . Yii::t('art', 'AIS') . '</b> ' . Yii::$app->settings->get('general.title', 'Art Site'),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-static-top',
                'style' => 'margin-bottom: 0'
            ],
            'innerContainerOptions' => [
                'class' => 'container-fluid'
            ]
        ]);

        $menuItems = [
            ['label' => '<i class="fa fa-home" style="margin-right: 5px;"></i>' . Yii::t('art', 'Home'), 'url' => Yii::$app->urlManager->hostInfo],
        ];
        // $menuItems = Menu::getMenuItems('main-menu');
        if (Yii::$app->user->isGuest) {

            $menuItems[] = [
                'label' => '<i class="fa fa-paper-plane-o" style="margin-right: 5px;"></i>' . Yii::t('art/auth', 'Contact'),
                'url' => \yii\helpers\Url::to(['/site/contact'])
            ];
            $menuItems[] = [
                'label' => '<i class="fa fa-user-plus" style="margin-right: 5px;"></i>' . Yii::t('art/auth', 'Signup'),
                'url' => \yii\helpers\Url::to(['/auth/default/finding'])
            ];
            $menuItems[] = [
                'label' => '<i class="fa fa-sign-in" style="margin-right: 5px;"></i>' . Yii::t('art/auth', 'Enter'),
                'url' => \yii\helpers\Url::to(['/auth/default/login'])
            ];
        } else {
            $avatar = ($userAvatar = Yii::$app->user->identity->getAvatar('small')) ? $userAvatar : AvatarAsset::getDefaultAvatar('small');
            $menuItems[] = [
                'label' => '<img src="' . $avatar . '" class="user-image" alt="User Image"/>' . Yii::$app->user->identity->username,
                'url' => ['/auth/default/profile'],
            ];

            if (!Yii::$app->session->has(\artsoft\user\controllers\DefaultController::ORIGINAL_USER_SESSION_KEY)) {
                $menuItems[] = [
                    'label' => '<i class="fa fa-sign-out" style="margin-right: 5px;"></i>' . Yii::t('art/auth', 'Logout'),
                    'url' => ['/auth/default/logout', 'language' => false],
                    'linkOptions' => ['data-method' => 'post']
                ];
            } else {
                $menuItems[] = [
                    'label' => '<span style="color: white;"><i class="fa fa-user-secret" style="margin-right: 5px;"></i>' . Yii::t('art/auth', 'Logout') . '</span>',
                    'url' => '/admin/user/default/impersonate',
                    'linkOptions' => ['data-method' => 'post'],
                    'options' => ['style' => 'background-color: #e28b00;'],
                ];
            }

            $menuItems[] = [
                'label' => '<i class="fa fa-cogs"></i>',
                'url' => \yii\helpers\Url::to(['/admin']),
                'visible' => \artsoft\models\User::hasRole('viewDashboard'),
            ];
        }
        echo Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);

        echo LanguageSelector::widget(['display' => 'label', 'view' => 'pills']);

        NavBar::end();
        ?>
        <!-- SIDEBAR NAV -->
        <div class="navbar-default sidebar metismenu" role="navigation">
            <?php
            $menuItemsKey = Yii::$app->user->isGuest ? '__guestMenuItems' . Yii::$app->language : '__mainMenuItems' . Yii::$app->language;
            if (!$menuItems = Yii::$app->cache->get($menuItemsKey)) {
                Yii::$app->cache->set($menuItemsKey, $menuItems, 3600);
            }
            echo Navigation::widget([
                'encodeLabels' => false,
                'dropDownCaret' => '<span class="arrow"></span>',
                'options' => [
                    ['class' => 'nav side-menu'],
                    ['class' => 'nav nav-second-level'],
                    ['class' => 'nav nav-third-level']
                ],
                'items' => [],
            ])
            ?>
        </div>
        <!-- !SIDEBAR NAV -->
    </nav>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>

                    <?php if (Yii::$app->session->hasFlash('crudMessage')): ?>
                        <div class="alert alert-info alert-dismissible alert-crud" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= Yii::$app->session->getFlash('crudMessage') ?>
                        </div>
                    <?php endif; ?>
                    <?= Alert::widget() ?>
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>

</div>
<footer class="footer">
    <div class="container">
        <p class="pull-left">
            &copy; <?= '<b>' . Yii::t('art', 'AIS') . '</b> ' . Html::encode(Yii::$app->settings->get('general.title', 'Art Site')) ?>
            2009-<?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?>
            , <?= artsoft\Art::powered() . ' ' . Yii::$app->params['version'] ?></p>
    </div>
</footer>

<!--кнопка вверх-->
<?= \artsoft\widgets\ScrollupWidget::widget() ?>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
