<?php

namespace artsoft\widgets;

use yii\authclient\SessionStateStorage;
use yii\base\Widget;

class Notice extends Widget
{

    public function run()
    {
        $s = new SessionStateStorage();
        $list = $s->get('notice');
        $content = $this->render('notice', ['list' => $list ? $list : []]);
        $s->remove('notice'); // очищаем список
        return $content;
    }

    public static function registerDanger($message, $title = 'Внимание')
    {
        self::register($message, $title, 'danger', 'ban');
    }

    public static function registerWarning($message, $title = 'Предупреждение')
    {
        self::register($message, $title, 'warning', 'warning');
    }

    public static function registerInfo($message, $title = 'Информация')
    {
        self::register($message, $title, 'info', 'info');
    }

    public static function registerSuccess($message, $title = 'Сообщение')
    {
        self::register($message, $title, 'success', 'check');
    }

    public static function register($message, $title, $type, $icon)
    {
        $s = new SessionStateStorage();

        $list = $s->get('notice');
        $list[] = array(
            'message' => $message,
            'title' => $title,
            'type' => $type,
            'icon' => $icon
        );
        $s->set('notice', $list);
    }
}
