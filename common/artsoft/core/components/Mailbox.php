<?php

namespace artsoft\components;

use artsoft\helpers\RefBook;
use artsoft\models\User;
use common\models\user\UsersView;
use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

/**
 * Class Mailbox
 *
 * Usage examples:
 * ~~~
 * Yii::$app->mailbox->send(1000,'Title','Content');
 * Yii::$app->mailbox->send([1000,1001],'Title','Content');
 * ~~~
 * @package artsoft\components
 */
class Mailbox extends Component
{
    public $modelClass = 'artsoft\mailbox\models\Mailbox';

    protected $teachers_id;
    protected $teachers_io;
    protected $teachers_sender_fio;
    protected $sign_message;
    protected $model;
    protected $module;
    protected $action;
    protected $isAdmin;

    public function mailing($receiversIds, $content = NULL, $title = NULL)
    {
        if (!$receiversIds) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр receiversIds.");
        }
        $m = new $this->modelClass;
        $m->scenario = $this->modelClass::SCENARIO_COMPOSE;
        $m->status_post = $this->modelClass::STATUS_POST_SENT;
        $m->receivers_ids = $this->getReceivers($receiversIds);
        $m->title = $title == NULL ? '<b>Сообщение модуля "Рассылка"</b>' : $title;
        $m->content = $content;

        return $m->save();
    }

    /**
     * @param array|int $receiversIds Получатели сообщения
     * @param null $model Модель
     * @param null $content Сообщение
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function send($receiver_id, $action = 'approve', $model = NULL, $content = NULL)
    {
        if (!$receiver_id) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр receiver_id.");
        }
        if (is_array($receiver_id)) {
            throw new NotFoundHttpException("Параметр receiver_id не должен быть массивом. Используйте метод mailing для рассылок");
        }
        $this->model = $model;
        $this->action = $action;
        $this->module = $this->model ? StringHelper::basename($model::className()) : null;
        $this->teachers_id = RefBook::find('users_teachers')->getValue($receiver_id) ?? null;
        $teachers_sender_id = RefBook::find('users_teachers')->getValue(Yii::$app->user->identity->id) ?? null;
        $this->teachers_sender_fio = RefBook::find('teachers_fio')->getValue($teachers_sender_id);
        $this->sign_message = $content;
        $this->isAdmin = in_array($receiver_id, User::getUsersByRole('administrator'));

        $m = new $this->modelClass;
        $m->scenario = $this->modelClass::SCENARIO_COMPOSE;
        $m->status_post = $this->modelClass::STATUS_POST_SENT;
        $m->receivers_ids = $this->getReceiver($receiver_id);
        $m->title = $this->getTitle();
        $m->content = $this->getContent();

        return $m->save();
    }

    /**
     * @param $receiversIds
     * @return mixed
     */
    protected function getReceivers($receiversIds)
    {
        return User::find()
            ->where(['id' => $receiversIds])
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->column();
    }

    protected function getReceiver($receiver_id)
    {
        $user = UsersView::find()
            ->where(['id' => $receiver_id])
            ->andWhere(['user_category' => 'teachers'])
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->one();
        if ($user) {
            $this->teachers_io = $user->first_name . ' ' . $user->middle_name;
            return [$user->id];
        }
        return [];
    }

    protected function getTitle()
    {
        switch ($this->module) {
            case 'Schedule':
                $text = 'Расписание занятий';
                break;
            case 'ScheduleConsult':
                $text = 'Расписание консультаций';
                break;
            case 'Schoolplan':
                $text = 'План работы школы';
                break;
            case 'SchoolplanPerform':
                $text = 'Выполнение плана и участие в меропритиях';
                break;
            case 'Thematic':
                $text = 'Тематические(репертуарные) планы';
                break;
            default:
                $text = '';
                break;
        }
        return 'Сообщение модуля "' . $text . '"';
    }

    protected function getLink()
    {
        $link = null;
        switch ($this->module) {
            case 'Schoolplan':
                $link = Yii::$app->urlManager->hostInfo . ($this->isAdmin != true ? '/schoolplan/default/update?id=' : '/admin/schoolplan/default/update?id=') . $this->model->id;
                break;
            case 'SchoolplanPerform':
                $modelSchoolplan = $this->model->schoolplan;
                $link = Yii::$app->urlManager->hostInfo . ($this->isAdmin != true ? '/schoolplan/default/perform?mode=update&id=' : '/admin/schoolplan/default/perform?mode=update&id=') . $modelSchoolplan->id . '&objectId=' . $this->model->id;
                break;
//            case 'Schedule':
//                $text = 'Расписание занятий';
//                break;
//            case 'ScheduleConsult':
//                $text = 'Расписание консультаций';
//                break;
//            case 'Thematic':
//                $text = 'Тематические(репертуарные) планы';
//                break;
        }
        return $link;
    }
    protected function getContent()
    {
        $link = $this->getLink();
        $htmlBody = '<p><b>Здравствуйте, ' . Html::encode($this->teachers_io) . '</b></p>';
        $htmlBody .= '<hr>';
        switch (1) {
            case $this->module == 'Schoolplan' && $this->action == 'modif':
                $htmlBody .= '<p><b>Прошу Вас доработать мероприятие: </b>' .  $this->model->title . ' за ' . $this->model->datetime_in . '</p>';
                break;
            case $this->module == 'Schoolplan' && $this->action == 'approve':
                $htmlBody .= '<p><b>Мероприятие: </b>' .  $this->model->title . ' за ' . $this->model->datetime_in . ' <b>было утверждено.</b></p>';
                break;
            case $this->module == 'Schoolplan' && $this->action == 'send_approve':
                $htmlBody .= '<p><b>Прошу Вас утвердить мероприятие: </b>' .  $this->model->title . ' за ' . $this->model->datetime_in . '.</p>';
                break;
            case $this->module == 'SchoolplanPerform' && $this->action == 'modif':
                $modelSchoolplan = $this->model->schoolplan;
                $htmlBody .= '<p><b>Прошу Вас доработать карточку выполнения плана и участия в мероприятии: </b>' . $modelSchoolplan->title  . ' за ' . $modelSchoolplan->datetime_in . '</p>';
                break;
            case $this->module == 'SchoolplanPerform' && $this->action == 'approve':
                $modelSchoolplan = $this->model->schoolplan;
                $htmlBody .= '<p><b>Карточка выполнения плана и участия в мероприятии: </b>' .  $modelSchoolplan->title . ' за ' . $modelSchoolplan->datetime_in . ' <b>была согласована.</b></p>';
                break;
            case $this->module == 'SchoolplanPerform' && $this->action == 'send_approve':
                $modelSchoolplan = $this->model->schoolplan;
                $htmlBody .= '<p><b>Прошу Вас утвердить карточку выполнения плана и участия в мероприятии: </b>' .  $modelSchoolplan->title . ' за ' . $modelSchoolplan->datetime_in . '.</p>';
                break;
//            case 'Schedule':
//                $htmlBody .= '<p>Прошу Вас внести уточнения в Расписание занятий на:' . strip_tags(ArtHelper::getStudyYearsValue($plan_year)) . ' учебный год. ' . '</p>';
//                $link = Yii::$app->urlManager->hostInfo . '/teachers/schedule-items/index?id=' . $this->teachers_id;
//                break;
//            case 'ScheduleConsult':
//                $htmlBody .= '<p>Прошу Вас внести уточнения в Расписание консультаций на:' . strip_tags(ArtHelper::getStudyYearsValue($plan_year)) . ' учебный год. ' . '</p>';
//                $link = Yii::$app->urlManager->hostInfo . '/teachers/consult-items/index?id=' . $this->teachers_id;
//                break;
//            case 'Thematic':
//                $htmlBody .= '<p>Прошу Вас доработать тематический(репертуарный) план' . '</p>';
//                $link = '';
//                break;

        }

        $htmlBody .= '<p>' . $this->sign_message . '</p>';
        $htmlBody .= '<hr>';
        $htmlBody .= '<p>Пройдите по ссылке: ' . Html::a(Html::encode($link), $link, [ 'target' => '_blank', 'data-pjax' => '0']) . ' (откроется в новом окне)</p>';
        $htmlBody .= '<hr>';
        $htmlBody .= '<p><b>С уважением, ' . Html::encode($this->teachers_sender_fio) . '</b></p>';
        return $htmlBody;
    }
}