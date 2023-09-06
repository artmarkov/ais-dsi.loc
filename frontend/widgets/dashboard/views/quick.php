<?php

use artsoft\helpers\Html;

/* @var $this yii\web\View */
?>

<div class="panel panel-default dw-widget">
    <div class="panel-heading">Быстрые действия</div>
    <div class="panel-body">
        <div class="form-group btn-group">
            <?= Html::a(
                '<i class="fa fa-calendar" aria-hidden="true"></i> Сетка расписания школы',
                ['/schedule/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-hourglass-start" aria-hidden="true"></i> ' . Yii::t('art/guide', 'Entrant'),
                ['/entrant/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-calendar" aria-hidden="true"></i> ' . Yii::t('art/calendar', 'Activities calendar'),
                ['/activities/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-paper-plane-o" aria-hidden="true"></i> ' . Yii::t('art/studyplan', 'Individual plans'),
                ['/teachers/studyplan/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-paper-plane-o" aria-hidden="true"></i> ' . Yii::t('art/studyplan', 'Individual plans'),
                ['/studyplan/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-users" aria-hidden="true"></i> Мои ученики',
                ['/sect/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
        </div>
    </div>
</div>