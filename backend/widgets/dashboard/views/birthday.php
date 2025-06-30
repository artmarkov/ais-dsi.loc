<?php

/* @var $this yii\web\View */
//echo '<pre>' . print_r($models, true) . '</pre>';die();
use artsoft\helpers\ArtHelper; ?>

<div class="panel panel-default dw-widget">
    <div class="panel-heading">Дни рождения на сегодня <span
                style="color: #921e12"><?= \Yii::$app->formatter->asDate(time()) ?></span>
    </div>
    <div class="panel-body">
        <?php if ($models): ?>
            <div class="clearfix">
            <?php
            $text = '';
            foreach ($models as $item => $model) {
                $age = ArtHelper::age($model['birth_date']);
                $text .= strip_tags($model['category_name']) . ': ' . strip_tags($model['fullname']) . ' - ' . $age['age_year'] . ' ' . ArtHelper::per($age['age_year']) . '<BR/> ';
            }
            echo $text;
            ?>
                </div>
        <?php else: ?>
            <h5><em><?= 'Не найдено записей.' ?></em></h5>
        <?php endif; ?>
    </div>
</div>