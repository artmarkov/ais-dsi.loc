<?php

namespace artsoft\widgets;

use yii\base\Widget;

class Tooltip extends Widget
{
    public $message;
    public $type = 'danger';
    protected $color;
    protected $icon;

    public function run()
    {
        switch ($this->type) {
            case 'danger':
                $this->color = 'red';
                $this->icon = 'warning';
                break;
            case 'warning':
                $this->color = 'orange';
                $this->icon = 'warning';
                break;
            case 'info':
                $this->color = 'blue';
                $this->icon = 'warning';
                break;
            case 'success':
                $this->color = 'green';
                $this->icon = 'warning';
                break;

        }
        return $this->render('tooltip', ['v' => [
            'message' => $this->message,
            'color' => $this->color,
            'icon' => $this->icon
        ]]);
    }
}
