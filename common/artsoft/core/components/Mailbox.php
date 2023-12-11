<?php

namespace artsoft\components;

use artsoft\helpers\ArtHelper;
use artsoft\models\User;
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

    protected $teachers_io;
    protected $teachers_sender_fio;
    protected $sign_message;
    protected $model;
    protected $module;

    /**
     * @param array|int $receiversIds Получатели сообщения
     * @param null $model Модель
     * @param null $content Сообщение
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function send($receiversIds, $model = NULL, $content = NULL)
    {
        if (!$receiversIds) {
            throw new NotFoundHttpException("Отсутствует обязательный параметр receiversIds.");
        }
        $this->model = $model;
        $this->module = $this->model ? StringHelper::basename($model::className()) : null;
        $title = '<b>Сообщение модуля "' . $this->getModuleText() . '"</b>';
        $m = new $this->modelClass;
        $m->scenario = $this->modelClass::SCENARIO_COMPOSE;
        $m->status_post = $this->modelClass::STATUS_POST_SENT;
        $m->receivers_ids = $this->getReceivers($receiversIds);
        $m->title = $title;
        $m->content = $content;

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

    protected function getModuleText()
    {
        switch ($this->module) {
            case 'Schedule':
                return 'Расписание занятий';
                break;
            case 'ScheduleConsult':
                return 'Расписание консультаций';
                break;
            case 'Schoolplan':
                return 'План работы школы';
                break;
            case 'SchoolpanPerform':
                return 'Выполнение плана и участие в меропритиях';
                break;
            case 'Thematic':
                return 'Тематические(репертуарные) планы';
                break;
            default:
                return '';
        }
    }

    protected function getLink()
    {
        switch ($this->module) {
            case 'Schedule':
                return Yii::$app->urlManager->hostInfo . '/teachers/schedule-items/index?id=' . $teachers_id;
                break;
            case 'ScheduleConsult':
                return Yii::$app->urlManager->hostInfo . '/teachers/consult-items/index?id=' . $teachers_id;
                break;
            case 'Schoolplan':
                return 'План работы школы';
                break;
            case 'SchoolpanPerform':
                return 'Выполнение плана и участие в меропритиях';
                break;
            case 'Thematic':
                return 'Тематические(репертуарные) планы';
                break;
            default:
                return '';
        }
    }

    protected function getContent()
    {
        $htmlBody = '<p><b>Здравствуйте, ' . Html::encode($this->teachers_io) . '</b></p>';
        $htmlBody .= '<p>Прошу Вас внести уточнения в ' . $this->getModuleText($this->module) . ' на:' . strip_tags(ArtHelper::getStudyYearsValue($plan_year)) . ' учебный год. ' . '</p>';
        $htmlBody .= '<p>' . $this->sign_message . '</p>';
        $htmlBody .= '<p>' . Html::a(Html::encode($link), $link) . '</p>';
        $htmlBody .= '<hr>';
        $htmlBody .= '<p><b>С уважением, ' . Html::encode($this->teachers_sender_fio) . '</b></p>';

        return $htmlBody;
    }
}