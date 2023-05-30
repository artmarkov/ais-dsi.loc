<?php

use artsoft\helpers\Html;

/* @var $this yii\web\View */
?>

<div class="panel panel-default dw-widget">
    <div class="panel-heading">Быстрые действия</div>
    <div class="panel-body">
        <div class="form-group btn-group">
            <?= Html::a(
                '<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('art/student', 'Student Registration'),
                ['/students/default/finding'],
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
                ['/studyplan/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-users" aria-hidden="true"></i> ' . Yii::t('art/guide', 'Subject Sects'),
                ['/sect/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
        </div>
    </div>
</div>