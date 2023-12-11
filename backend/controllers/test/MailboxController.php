<?php

namespace backend\controllers\test;

use artsoft\helpers\ArtHelper;
use artsoft\helpers\RefBook;
use Yii;
use yii\helpers\Html;

class MailboxController extends \backend\controllers\DefaultController
{
    public function actionIndex()
    {
        $plan_year = 2023;
        $sign_message = 'Необходимо сделать следующее...';
        $title = '<b>Сообщение модуля "Расписание консультаций"</b>';
        $receiverId = 1136;
       // $senderId =  Yii::$app->user->identity->id;
        $senderId = 1267;
        $teachers_id = RefBook::find('users_teachers')->getValue($receiverId) ?? null;
        $teachers_fio = RefBook::find('teachers_fullname')->getValue($teachers_id);
        $teachers_sender_id = RefBook::find('users_teachers')->getValue($senderId) ?? null;
        $teachers_sender_fio = RefBook::find('teachers_fio')->getValue($teachers_sender_id);
        $link = Yii::$app->urlManager->hostInfo . '/teachers/consult-items/index?id=' . $teachers_id;

        $htmlBody = '<p><b>Здравствуйте, ' . Html::encode($teachers_fio) . '</b></p>';
        $htmlBody .= '<p>Прошу Вас внести уточнения в Расписание консультаций на:' . strip_tags(ArtHelper::getStudyYearsValue($plan_year)) . ' учебный год. ' . '</p>';
        $htmlBody .= '<p>' . $sign_message . '</p>';
        $htmlBody .= '<p>' . Html::a(Html::encode($link), $link) . '</p>';
        $htmlBody .= '<hr>';
        $htmlBody .= '<p><b>С уважением, ' . Html::encode($teachers_sender_fio) . '</b></p>';

        Yii::$app->mailbox->send($receiverId, $title, $htmlBody);
    }

}