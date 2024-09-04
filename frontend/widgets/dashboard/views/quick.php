<?php

use artsoft\helpers\Html;
use artsoft\models\User;

/* @var $this yii\web\View */
$studyplan_id = null;

$userId = Yii::$app->user->identity ? Yii::$app->user->identity->getId() : null;

if (\artsoft\models\User::hasRole(['student'])) {
    $student_id = \artsoft\helpers\RefBook::find('users_students')->getValue($userId) ?? null;
    $studyplan_id = \common\models\studyplan\Studyplan::getStudentStudyplanDefault($student_id);

} elseif (\artsoft\models\User::hasRole(['parents'])) {
    $parents_id = \artsoft\helpers\RefBook::find('users_parents')->getValue($userId) ?? null;
    $studyplan_id = \common\models\studyplan\Studyplan::getParentStudyplanDefault($parents_id);
}
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
            <?php if ($studyplan_id): ?>
                <?= Html::a(
                    '<i class="fa fa-calendar-check-o" aria-hidden="true"></i> Расписание занятий',
                    ['/studyplan/default/schedule-items', 'id' => $studyplan_id],
                    [
                        'class' => 'btn btn-info btn-lg',
                    ]
                );
                ?>
                <?= Html::a(
                    '<i class="fa fa-calendar-check-o" aria-hidden="true"></i> Расписание занятий',
                    ['/parents/studyplan/schedule-items', 'id' => $studyplan_id],
                    [
                        'class' => 'btn btn-info btn-lg',
                    ]
                );
                ?>
            <?php endif;?>
            <?= Html::a(
                '<i class="fa fa-hourglass-start" aria-hidden="true"></i> ' . Yii::t('art/guide', 'Entrant'),
                ['/entrant/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-calendar-check-o" aria-hidden="true"></i> Календарь мероприятий',
                ['/activities/student-schedule/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                    'visible' => User::hasRole(['student','parents'])
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-calendar" aria-hidden="true"></i> ' . Yii::t('art/calendar', 'Activities calendar'),
                ['/activities/default/calendar'],
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
                '<i class="fa fa-paper-plane-o" aria-hidden="true"></i> ' . Yii::t('art/studyplan', 'Individual student plans'),
                ['/studyplan/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-paper-plane-o" aria-hidden="true"></i> ' . Yii::t('art/studyplan', 'Individual plans'),
                ['/parents/studyplan/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-address-book-o" aria-hidden="true"></i> Карточка родителя(официального представителя)',
                ['/parents/default/index'],
                [
                    'class' => 'btn btn-default btn-lg',
                ]
            );
            ?>
            <?php if ($studyplan_id): ?>
            <?= Html::a(
                '<i class="fa fa-money" aria-hidden="true"></i> Оплата за обучение',
                ['/studyplan/default/studyplan-invoices', 'id' => $studyplan_id],
                [
                    'class' => 'btn btn-success btn-lg',
                ]
            );
            ?>
            <?= Html::a(
                '<i class="fa fa-money" aria-hidden="true"></i> Оплата за обучение',
                ['/parents/studyplan/studyplan-invoices', 'id' => $studyplan_id],
                [
                    'class' => 'btn btn-success btn-lg',
                ]
            );
            ?>
            <?php endif;?>
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