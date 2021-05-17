<?php

use backend\assets\AppAsset;
use artsoft\assets\MetisMenuAsset;
use artsoft\assets\ArtAsset;
use artsoft\user\controllers\DefaultController;
use artsoft\widgets\LanguageSelector;
use artsoft\widgets\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use artsoft\models\Request;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
$assetBundle = ArtAsset::register($this);
MetisMenuAsset::register($this);
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
<?= artsoft\widgets\PaceWidget::widget(); ?>
<div class="wrap">

    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

        <?php
        $logo = $assetBundle->baseUrl . '/images/art-logo-inverse.png';
        NavBar::begin([
            'brandLabel' => Html::img($logo, ['class' => 'art-logo', 'alt' => 'ArtSoft CMS']) . '<b>ArtSoft</b> ' . Yii::t('art', 'Control Panel'),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-static-top', //navbar-inverse navbar-default
                'style' => 'margin-bottom: 0'
            ],
            'innerContainerOptions' => [
                'class' => 'container-fluid'
            ]
        ]);

        $menuItems = [
            ['label' => str_replace('http://', '', Yii::$app->urlManager->hostInfo), 'url' => Yii::$app->urlManager->hostInfo],
        ];

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => '<i class="fa fa-sign-in" style="margin-right: 5px;"></i>' . Yii::t('art', 'Login'), 'url' => ['/auth/login']];
        } elseif (!Yii::$app->session->has(DefaultController::ORIGINAL_USER_SESSION_KEY)) {
            $menuItems[] = [
                'label' => '<i class="fa fa-sign-out" style="margin-right: 5px;"></i>' . Yii::t('art', 'Logout {username}', ['username' => Yii::$app->user->identity->username]),
                'url' => Yii::$app->urlManager->hostInfo . '/auth/logout',
                'linkOptions' => ['data-method' => 'post']
            ];
        } else {
            $menuItems[] = [
                'label' => '<span style="color: white;"><i class="fa fa-user-secret" style="margin-right: 5px;"></i>' . Yii::t('art', 'Logout {username}', ['username' => Yii::$app->user->identity->username]) . '</span>',
                'url' => 'user/default/impersonate',
                'linkOptions' => ['data-method' => 'post'],
                'options' => ['style' => 'background-color: #e28b00;'],
            ];
        }


        echo Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);

        echo LanguageSelector::widget(['display' => 'label']);

        NavBar::end();
        ?>
        <?= $this->render('left.php') ?>
    </nav>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <?= \yii2mod\notify\BootstrapNotify::widget([
                        'clientOptions' => [
                            'offset' => [
                                'x' => 20,
                                'y' => 50,
                            ],
                        ]
                    ]);
                    ?>
                    <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
                    <?php if (isset($this->params['tabMenu']) && $this->params['tabMenu']): ?>
                        <div class="nav-tabs-custom">
                            <?= \artsoft\widgets\Nav::widget([
                                'encodeLabels' => false,
                                'activeClass' => 'active',
                                'options' => [
                                    ['class' => 'nav nav-tabs'],
                                    ['class' => 'dropdown-menu'],
                                ],
                                'items' => $this->params['tabMenu'],
                            ]) ?>

                            <div class="tab-content">
                                <?= $content ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <?= $content ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
<footer class="footer">
    <div class="container">
        <a title="debug" class="text-sm" href="<?= \yii\helpers\Url::to(['/debug']) ?>"><i class="fa fa-bug"></i></a>
        <?php if (Request::$request): ?>
            <span class="text-sm"><i class="fa fa-tag"></i><?= Request::$request->id ?></span>
            <span class="text-sm"><i class="fa fa-clock-o"></i><?= round(Request::getTimeSpent(), 2) ?>s</span>
        <?php endif; ?>
    </div>
</footer>
<!--up button-->
<?= artsoft\widgets\ScrollupWidget::widget() ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
