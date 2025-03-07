<?php

use yii\helpers\Html;
use artsoft\block\models\Block;

/* @var $this yii\web\View */

$this->title = Block::getTitle('main-info');
?>
<?php
$pre_status = Yii::$app->settings->get('module.pre_status');
$pre_date_in = Yii::$app->formatter->asTimestamp(Yii::$app->settings->get('module.pre_date_in'));
$pre_date_out = Yii::$app->formatter->asTimestamp(Yii::$app->settings->get('module.pre_date_out'));
//    print_r([$pre_status,$pre_date_in,$pre_date_out,time()]);
?>
<div class="site-index">
    <?php if (Yii::$app->user->isGuest): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                Быстрые действия
            </div>
            <div class="panel-body">
                <div class="form-group btn-group">
                    <?= (Yii::$app->user->isGuest && $pre_status == 1 && $pre_date_in < time() && $pre_date_out > time()) ?
                        Html::a(
                        '<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('art/guide', 'Reg Entrant'),
                        ['/preregistration/default/finding'],
                        [
                            'title' => 'Запись на обучение',
                            'class' => 'btn btn-default btn-lg',
                        ]
                    ) : null;
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
