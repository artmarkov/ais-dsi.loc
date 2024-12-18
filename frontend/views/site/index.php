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
                Быстрые действия
            </div>
            <div class="panel-body">
                <div class="form-group btn-group">
                    <?= Html::a(
                        '<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('art/guide', 'Reg Entrant'),
                        ['/preregistration/default/finding'],
                        [
                            'title' => 'Запись на обучение',
                            'class' => 'btn btn-default btn-lg',
                        ]
                    );
                    ?>
                    <?= Html::a(
                        '<i class="fa fa-sign-in" aria-hidden="true"></i> ' . 'Вход в АИС',
                        ['/auth/default/login'],
                        [
                            'class' => 'btn btn-info btn-lg',
                        ]
                    );
                    ?>
                    <?= Html::a(
                        '<i class="fa fa-user-plus" aria-hidden="true"></i> ' . 'Регистрация',
                        ['/auth/default/finding'],
                        [
                            'class' => 'btn btn-warning btn-lg',
                        ]
                    );
                    ?>
                    <?= Html::a(
                        '<i class="fa fa-paper-plane-o" aria-hidden="true"></i> ' . 'Обратная звязь',
                        ['/site/contact'],
                        [
                            'class' => 'btn btn-danger btn-lg',
                        ]
                    );
                    ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $this->title?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= Block::getHtml('main-info', ['general_title' => Yii::$app->settings->get('general.title', 'Art Site')]) ?>
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
